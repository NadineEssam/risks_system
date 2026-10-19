<?php

use App\Support\PermissionRegistry;

// نفس PerUser() في نظام الشكاوى — للـ sidebar وأزرار الـ DataTables.
if (! function_exists('PerUser')) {
    function PerUser(string $routeName): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can(PermissionRegistry::resolve($routeName));
    }
}