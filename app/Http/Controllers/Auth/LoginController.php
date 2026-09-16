<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * تسجيل الدخول عبر LDAP (نفس أسلوب نظام خدمة العملاء): يُبحث عن المستخدم
 * محلياً بواسطة userID، ثم يُتحقق من كلمة المرور عبر محاولة الربط (bind)
 * بخوادم LDAP الموضحة في config/ldap.php بالتتابع حتى ينجح أحدها. لا تُستخدم
 * كلمة المرور المحلية المخزَّنة في قاعدة البيانات إطلاقاً في هذا المسار.
 */
class LoginController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'userID' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'userID.required' => 'حقل اسم المستخدم مطلوب.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
        ]);

        // العمود الفعلي بقاعدة البيانات هو domain_username (بعد إعادة
        // تسميته من userID القديم) — حقل الفورم/الطلب لسه اسمه userID
        // عمداً لتفادي تغيير resources/views/auth/login.blade.php.
        $user = User::where('domain_username', $credentials['userID'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'userID' => 'المستخدم غير مسجل في النظام.',
            ]);
        }

        if (! $this->authenticateAgainstLdap($credentials['userID'], $credentials['password'])) {
            throw ValidationException::withMessages([
                'userID' => 'خطأ في اسم المستخدم أو كلمة المرور. يرجى المحاولة مرة أخرى.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        if (! Auth::user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'userID' => 'هذا الحساب غير مُفعَّل، برجاء التواصل مع مسئول النظام.',
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * يحاول الربط (bind) بكل خادم LDAP بالترتيب حتى ينجح أحدها.
     */
    private function authenticateAgainstLdap(string $username, string $password): bool
    {
        $port = config('ldap.port');
        $domainPrefix = config('ldap.domain_prefix');

        foreach (config('ldap.hosts', []) as $host) {
            $ldapConn = @ldap_connect("ldap://{$host}:{$port}");

            if (! $ldapConn) {
                continue;
            }

            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            $ldapRdn = "{$domainPrefix}\\{$username}";

            if (@ldap_bind($ldapConn, $ldapRdn, $password)) {
                return true;
            }
        }

        return false;
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}