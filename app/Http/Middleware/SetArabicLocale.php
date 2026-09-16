<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * النظام بالكامل باللغة العربية فقط طبقاً لمتطلبات الوثيقة
 * (قسم "لغة النظام")، لذا يتم تثبيت اللغة والاتجاه هنا دون الحاجة لمبدّل لغة.
 */
class SetArabicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale('ar');

        return $next($request);
    }
}
