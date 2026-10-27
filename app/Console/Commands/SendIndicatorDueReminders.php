<?php

namespace App\Console\Commands;

use App\Models\Indicator;
use App\Notifications\SystemNotification;
use App\Reports\IndicatorComplianceReport;
use App\Support\Notifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * تنبيهات قرب/تأخر موعد قياس مؤشرات المخاطر — بيشتغل يومياً.
 * الفترات من تاريخ الاعتماد حسب دورية الإبلاغ (نفس تقرير الانتظام).
 */
class SendIndicatorDueReminders extends Command
{
    protected $signature = 'indicators:due-reminders {--date= : تاريخ التشغيل للاختبار (Y-m-d)}';

    protected $description = 'إشعارات قرب/تأخر موعد قياس مؤشرات المخاطر';

    /** قبل الاستحقاق بكام يوم */
    public const DAYS_BEFORE = [7, 2];

    /** بعد الاستحقاق بكام يوم */
    public const DAYS_AFTER = [1, 7];

    public function handle(): int
    {
        $today = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::today();

        $sent = 0;

        $indicators = Indicator::active()
            ->whereNotNull('approval_date')
            ->with(['reportingFrequency', 'potentialRiskRegister.sectorDetails', 'followups'])
            ->get();

        foreach ($indicators as $indicator) {
            $months = IndicatorComplianceReport::FREQUENCY_MONTHS[trim((string) $indicator->reportingFrequency?->frequency_name)] ?? null;

            if (! $months) {
                continue;
            }

            // تاريخ إدخال كل قياس (ولو مش موجود: تاريخ القياس) — نفس تقرير الانتظام
            $entries = $indicator->followups
                ->map(fn ($f) => $f->creation_date ?? $f->measurement_date)
                ->filter()
                ->map(fn ($d) => Carbon::parse($d));

            $start = Carbon::parse($indicator->approval_date)->startOfDay();

            for ($i = 0; $i < 240; $i++) {
                $due       = $start->copy()->addMonthsNoOverflow($months);
                $fulfilled = $entries->contains(fn ($d) => $d->gte($start) && $d->lt($due));
                $daysLeft  = (int) round($today->diffInDays($due, false)); // + قبل الاستحقاق، - بعده

                if (! $fulfilled) {
                    if (in_array($daysLeft, self::DAYS_BEFORE, true)) {
                        $this->remind($indicator, $due, 'upcoming', $daysLeft);
                        $sent++;
                    }
                    if (in_array(-$daysLeft, self::DAYS_AFTER, true)) {
                        $this->remind($indicator, $due, 'overdue', -$daysLeft);
                        $sent++;
                    }
                }

                if ($due->gt($today)) {
                    break; // وصلنا للفترة الحالية
                }

                $start = $due;
            }
        }

        $this->info("تم إرسال {$sent} تنبيه — تاريخ التشغيل {$today->format('Y-m-d')}");

        return self::SUCCESS;
    }

    private function remind(Indicator $indicator, Carbon $due, string $type, int $days): void
    {
        $sectorIds = $indicator->potentialRiskRegister?->sectorDetails->pluck('sectors_sec_id')->all() ?? [];
        $sectorUsers = Notifier::sectorUsers($sectorIds);

        // مسئولو متابعة المؤشرات في القطاعات — ولو مفيش، كل مستخدمي القطاعات
        $officers = $sectorUsers->filter(fn ($u) => $u->hasRole('indicator-followup-officer'));
        $users = $officers->isNotEmpty() ? $officers : $sectorUsers;

        if ($type === 'overdue') {
            $users = $users->merge(Notifier::centralUsers());
        }

        $overdue = $type === 'overdue';

        // نفس التنبيه (مؤشر + تاريخ استحقاق + نوع + عدد أيام) مايتبعتش مرتين
        $key = "due:{$indicator->id}:{$due->format('Y-m-d')}:{$type}:{$days}";
        if (\Illuminate\Support\Facades\DB::table('notifications')->where('data', 'like', '%'.$key.'%')->exists()) {
            return;
        }

        // ⁦…⁩ بيعزل التاريخ عشان يظهر 2026-12-21 صح جوه الجملة العربي
        $dueText = "\u{2066}".$due->format('Y-m-d')."\u{2069}";

        Notifier::send($users, new SystemNotification(
            $overdue ? 'تأخر قياس مؤشر' : 'اقتراب موعد قياس مؤشر',
            ($overdue
                ? "انقضى موعد الاستحقاق {$dueText} منذ {$days} يوم دون إدخال قياس"
                : "متبقي {$days} يوم على موعد الاستحقاق {$dueText}")
                .' — المؤشر: '.Str::limit($indicator->indicator_name, 90),
            route('indicator-followups.index', $indicator),
            $overdue ? 'bi-alarm' : 'bi-calendar-event',
            $overdue ? 'danger' : 'warning',
            $key,
        ));
    }
}