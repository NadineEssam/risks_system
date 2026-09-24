<?php

namespace App\Http\Middleware;

use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// نفس فكرة نظام الشكاوى: اسم المسار هو الصلاحية.
class CheckRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $name = $request->route()?->getName();

        if ($name && ! PermissionRegistry::isExempt($name)) {
            abort_unless(
                $request->user()?->can(PermissionRegistry::resolve($name)),
                403,
                'ليس لديك صلاحية للوصول إلى هذه الصفحة.'
            );
        }

        return $next($request);
    }
}