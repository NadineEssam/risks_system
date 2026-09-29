<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Reports\ReportRegistry;
use App\Reports\IncidentFollowupsReport;
use App\Reports\IncidentsRegisterReport;
use App\Reports\IncidentsResolvedReport;
use App\Reports\IndicatorAlertsReport;
use App\Reports\IndicatorComplianceReport;

class AppServiceProvider extends ServiceProvider
{
        public function register(): void
    {
        // سجل التقارير — التقارير نفسها بتتسجل في الخطوة 3
        $this->app->singleton(ReportRegistry::class, function () {
            $registry = new ReportRegistry();

            // التقارير الخمسة (بنفس ترتيب ملف التقارير)
            $registry->register(
                new IncidentsRegisterReport(),
                new IncidentFollowupsReport(),
                new IndicatorAlertsReport(),
                new IncidentsResolvedReport(),
                new IndicatorComplianceReport(),
            );

            return $registry;
        });
    }

    public function boot(): void
    {


               
                // لو APP_URL فيه subpath (السيرفر: http://192.168.161.89/risk_management)
        // نثبّت الروابط عليه — محلياً APP_URL=http://localhost فمفيش حاجة بتتغير
        $appUrl   = rtrim((string) config('app.url'), '/');
        $basePath = parse_url($appUrl, PHP_URL_PATH);

        if ($basePath) {
            $this->app['request']->server->set('SCRIPT_NAME', $basePath.'/index.php');

            \Illuminate\Support\Facades\URL::forceRootUrl($appUrl);
            \Illuminate\Support\Facades\URL::forceScheme(parse_url($appUrl, PHP_URL_SCHEME) ?: 'http');
        }



        // اتجاه الصفحة الافتراضي RTL يُطبَّق من طبقة الـ Blade layout مباشرة،
        // ولا حاجة لأي منطق إضافي هنا حالياً بما أن اللغة الوحيدة هي العربية.

        Blade::directive('riskDegreeBadge', function ($e) {
            return "<span class=\"badge badge-{$e}\">{$e}</span>";
        });

        // اتجاه الصفحة الافتراضي RTL يُطبَّق من طبقة الـ Blade layout مباشرة،



        Paginator::useBootstrapFive();

        // اتجاه الصفحة الافتراضي RTL يُطبَّق من طبقة الـ Blade layout مباشرة،
        // ولا حاجة لأي منطق إضافي هنا حالياً بما أن اللغة الوحيدة هي العربية.

        Blade::directive('riskDegreeBadge', function ($expression) {
            return "<?php echo \App\Support\RiskDegreeHelper::badge($expression); ?>";
        });

        // مدير النظام (super-admin) يتجاوز كل فحوصات الصلاحيات تلقائياً.
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });






        
    }
}
