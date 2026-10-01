<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The notices a shopper has not cleared yet.
 *
 * A guest's cleared notices are remembered by session token, so the bell does
 * not keep showing something they have already read. The cap is what drives
 * the badge: it never shows a number above 99, it shows 99+.
 */
class NotificationService
{
    private const TOKEN_KEY = 'notifications.token';

    /** Badge cap. */
    public const BADGE_LIMIT = 99;

    public function __construct(private readonly Session $session) {}

    public function token(): string
    {
        if (! $this->session->has(self::TOKEN_KEY)) {
            $this->session->put(self::TOKEN_KEY, Str::random(40));
        }

        return (string) $this->session->get(self::TOKEN_KEY);
    }

    /**
     * Live notices the current viewer has not dismissed, newest first.
     *
     * The date window and the published flag apply to everyone: a draft or an
     * expired notice is not shown to the team either. Staff additionally see
     * the notices marked internal, which shoppers do not.
     */
    public function unread(): Collection
    {
        $dismissed = $this->dismissedNoticeIds();

        return Notice::query()
            ->live()
            ->when(! auth()->user()?->isStaff(), fn ($query) => $query->where('show_on_shop', true))
            ->latest('id')
            ->when($dismissed->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $dismissed))
            ->limit(self::BADGE_LIMIT + 1)
            ->get();
    }

    public function unreadCount(): int
    {
        return $this->unread()->count();
    }

    /**
     * The number for the badge: 99+ once there are more than 99.
     */
    public function badge(): ?string
    {
        $count = $this->unreadCount();

        return $count === 0 ? null : ($count > self::BADGE_LIMIT ? '99+' : (string) $count);
    }

    public function dismiss(Notice $notice): void
    {
        DB::table('notice_dismissals')->updateOrInsert(
            auth()->id()
                ? ['user_id' => auth()->id(), 'notice_id' => $notice->id]
                : ['session_id' => $this->token(), 'notice_id' => $notice->id],
            ['dismissed_at' => now()]
        );
    }

    /**
     * A row for the current person, whether signed in or a guest.
     *
     * @return array<string, mixed>
     */
    private function dismissalRow(int $noticeId): array
    {
        return [
            'user_id' => auth()->id(),
            'session_id' => auth()->id() ? null : $this->token(),
            'notice_id' => $noticeId,
            'dismissed_at' => now(),
        ];
    }

    /**
     * Clear everything the bell is currently showing.
     *
     * The rows are written, not removed: clearing an item is what makes it stop
     * showing, so deleting its row would put it straight back in the list.
     */
    public function dismissAll(): void
    {
        $unread = $this->unread()->pluck('id');

        if ($unread->isEmpty()) {
            return;
        }

        DB::table('notice_dismissals')->insert(
            $unread->map(fn ($id) => $this->dismissalRow((int) $id))->all()
        );
    }

    /**
     * Drop every clear this person has, so the bell fills up again.
     */
    public function forgetAll(): void
    {
        DB::table('notice_dismissals')
            ->when(auth()->id(), fn ($q) => $q->where('user_id', auth()->id()), fn ($q) => $q->where('session_id', $this->token()))
            ->delete();
    }

    public function dismissAllFor(Notice $notice): void
    {
        DB::table('notice_dismissals')->where('notice_id', $notice->id)->delete();
    }

    /**
     * Move a guest's cleared notices onto the account on sign-in, so the bell
     * does not resurrect what they already read.
     */
    public function mergeOnLogin(int $userId): void
    {
        DB::table('notice_dismissals')
            ->whereNull('user_id')
            ->where('session_id', $this->token())
            ->get()
            ->each(function (object $row) use ($userId) {
                $already = DB::table('notice_dismissals')
                    ->where('user_id', $userId)
                    ->where('notice_id', $row->notice_id)
                    ->exists();

                if ($already) {
                    DB::table('notice_dismissals')->where('id', $row->id)->delete();

                    return;
                }

                DB::table('notice_dismissals')->where('id', $row->id)->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            });
    }

    /**
     * @return Collection<int, int>
     */
    private function dismissedNoticeIds(): Collection
    {
        return DB::table('notice_dismissals')
            ->when(auth()->id(), fn ($q) => $q->where('user_id', auth()->id()), fn ($q) => $q->where('session_id', $this->token()))
            ->pluck('notice_id');
    }
}
