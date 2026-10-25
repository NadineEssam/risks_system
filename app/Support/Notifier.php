<?php

namespace App\Support;

use App\Models\Sector;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * تحديد المستلمين حسب القطاعات + الإرسال بعد ما الصفحة تتعرض للمستخدم.
 */
class Notifier
{
    /** مستخدمين فعّالين في قطاعات معيّنة (sec_id) */
    public static function sectorUsers(array $sectorIds): Collection
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $sectorIds))));

        return $ids ? User::where('is_active', 1)->whereIn('sector_id', $ids)->get() : collect();
    }

    /** مستخدمين قطاع المخاطر المركزي (كود 15) */
    public static function centralUsers(): Collection
    {
        $code   = config('app.central_risk_sector_code');
        $sector = $code ? Sector::where('sector_code', $code)->first() : null;

        return $sector ? self::sectorUsers([$sector->sec_id]) : collect();
    }

    /**
     * يبعت لكل المستلمين (من غير تكرار، ومن غير اللي عمل الحركة نفسه)
     * بعد ما الرد يوصل للمستخدم — والفشل مايوقفش النظام.
     */
    public static function send(Collection $users, SystemNotification $notification): void
    {
        $actorId = Auth::id();
        $users = $users->unique('id')->reject(fn ($u) => (int) $u->id === (int) $actorId)->values();

        if ($users->isEmpty()) {
            return;
        }

        dispatch(function () use ($users, $notification) {
            try {
                Notification::send($users, $notification);
            } catch (\Throwable $e) {
                Log::error('Notification failed: '.$e->getMessage());
            }
        })->afterResponse();
    }
}