<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\TeamAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Alerts the shop raises for itself, and who has cleared them.
 */
class TeamAlertService
{
    public const BADGE_LIMIT = 99;

    /**
     * Held for the request: the sidebar badge and the bell both need this list,
     * so it is worked out once rather than twice per page.
     *
     * @var Collection<int, TeamAlert>|null
     */
    private ?Collection $unreadCache = null;

    /**
     * Announce a new order to whoever is on the till.
     */
    public function orderPlaced(Order $order): TeamAlert
    {
        $order->loadMissing('items');

        return TeamAlert::create([
            'kind' => 'order',
            'title' => 'New order '.$order->order_number,
            'body' => sprintf(
                '%s · %s, %s · %s',
                $order->customer_name,
                $order->township,
                $order->itemCount().' '.($order->itemCount() === 1 ? 'item' : 'items'),
                $order->totalFormatted()
            ),
            'link' => route('admin.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Announce a registration to whoever can decide on it.
     *
     * Only an administrator or a manager may accept or turn an account down, so
     * the alert goes to them and to nobody else. The wording says what each
     * button does, because the decision is final on one side of it.
     */
    public function accountRegistered(User $user): TeamAlert
    {
        return TeamAlert::create([
            'kind' => 'account',
            'title' => 'New account: '.$user->name,
            'body' => sprintf(
                '%s wants an account as %s. Accept to let them sign in, or reject to close it for good.',
                $user->email,
                $user->role->label(),
            ),
            // Goes to the queue itself rather than to the person, because one
            // page lists everybody waiting and each alert can only point at one.
            'link' => route('admin.approvals.index'),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Alerts this person has not cleared yet, newest first.
     */
    public function unread(): Collection
    {
        if ($this->unreadCache !== null) {
            return $this->unreadCache;
        }

        return $this->unreadCache = TeamAlert::query()
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('team_alert_dismissals')
                    ->whereColumn('team_alert_dismissals.team_alert_id', 'team_alerts.id')
                    ->where('team_alert_dismissals.user_id', auth()->id());
            })
            // An account alert is about a decision only a manager or an
            // administrator may make, so plain staff are not shown it. The page
            // is refused for them anyway; this just keeps the bell honest.
            ->when(
                ! auth()->user()?->canManageCatalog(),
                fn (Builder $query) => $query->where('kind', '!=', 'account')
            )
            ->latest('id')
            ->limit(self::BADGE_LIMIT + 1)
            ->get();
    }

    public function unreadCount(): int
    {
        return $this->unread()->count();
    }

    public function badge(): ?string
    {
        $count = $this->unreadCount();

        return $count === 0 ? null : ($count > self::BADGE_LIMIT ? '99+' : (string) $count);
    }

    public function dismiss(TeamAlert $alert): void
    {
        DB::table('team_alert_dismissals')->updateOrInsert(
            ['user_id' => auth()->id(), 'team_alert_id' => $alert->id],
            ['dismissed_at' => now()]
        );

        $this->unreadCache = null;
    }

    public function dismissAll(): void
    {
        $unread = $this->unread()->pluck('id');

        if ($unread->isEmpty()) {
            return;
        }

        DB::table('team_alert_dismissals')->insert(
            $unread->map(fn (int $id) => [
                'user_id' => auth()->id(),
                'team_alert_id' => $id,
                'dismissed_at' => now(),
            ])->all()
        );
    }

    /**
     * Once an order has been dealt with, its alert is finished for everybody.
     *
     * Not a dismissal: this is the alert ceasing to be true. A paid order that
     * has been completed, cancelled, refunded or deleted has been handled, so
     * the entry is removed for the whole team rather than hidden from whoever
     * happened to click. Its dismissal rows go with it.
     */
    public function clearForOrder(int $orderId): void
    {
        $this->deleteAlerts(fn (Builder $query) => $query->where('order_id', $orderId));
    }

    /**
     * The same idea for an account: once it has been accepted or turned down
     * there is nothing left to decide, so the alert stops being true and goes
     * for everybody rather than sitting in somebody's bell.
     */
    public function clearForAccount(int $userId): void
    {
        $this->deleteAlerts(fn (Builder $query) => $query->where('user_id', $userId));
    }

    /**
     * @param  callable(Builder): void  $where
     */
    private function deleteAlerts(callable $where): void
    {
        $ids = TeamAlert::query()->tap($where)->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('team_alert_dismissals')->whereIn('team_alert_id', $ids)->delete();
        DB::table('team_alerts')->whereIn('id', $ids)->delete();

        $this->unreadCache = null;
    }
}
