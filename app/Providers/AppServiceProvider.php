<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
