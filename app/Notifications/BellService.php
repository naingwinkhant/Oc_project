<?php

namespace App\Notifications;

use Illuminate\Support\Collection;

/**
 * Everything the bell holds for the person looking at it.
 *
 * Two sources feed it: notices, which a manager writes, and alerts, which the
 * shop raises for itself such as a new order. They are merged into one list so
 * the panel, the badge and the "nothing new" state only have to work once.
 */
class BellService
{
    public function __construct(
        private readonly NotificationService $notices,
        private readonly TeamAlertService $alerts,
    ) {}

    /**
     * Newest first, whatever the source.
     *
     * Shoppers only ever get shopper-facing notices. Team alerts are for the
     * admin bell only: they carry order numbers, which have no business on a
     * page a shopper can be looking at, even when a member of staff is signed
     * in there.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function items(bool $withTeamAlerts = true): Collection
    {
        $items = $this->notices->unread()->map(fn ($notice) => [
            'kind' => 'notice',
            'id' => $notice->id,
            'title' => $notice->title,
            'body' => $notice->body,
            'link' => null,
            'icon' => 'bell',
            'tone' => $notice->toneClasses(),
        ]);

        if ($withTeamAlerts && $this->alertsBelongHere()) {
            $items = $items->concat(
                $this->alerts->unread()->map(fn ($alert) => [
                    'kind' => 'alert',
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'body' => $alert->body,
                    'link' => $alert->link,
                    'icon' => $alert->icon(),
                    'tone' => $alert->toneClasses(),
                ])
            );
        }

        return $items
            ->sortByDesc(fn (array $item) => $item['id'])
            ->values()
            ->take(self::LIMIT);
    }

    private const LIMIT = 100;

    public function count(bool $withTeamAlerts = true): int
    {
        return $this->items($withTeamAlerts)->count();
    }

    /**
     * 99 once there are more than that, never higher.
     */
    public function badge(bool $withTeamAlerts = true): ?string
    {
        $count = $this->count($withTeamAlerts);

        return $count === 0 ? null : ($count > 99 ? '99+' : (string) $count);
    }

    /**
     * Alerts are for the team, so only somebody signed in sees them.
     */
    private function alertsBelongHere(): bool
    {
        return (bool) auth()->user()?->isStaff();
    }
}
