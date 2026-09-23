<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // اسم المستخدم في الدومين (Active Directory) — يُستخدم لتسجيل
            // الدخول عبر LDAP بدلاً من كلمة مرور محلية (نفس أسلوب نظام
            // خدمة العملاء). يجب أن يطابق اسم مستخدم الدومين الفعلي للموظف.
            // اسم العمود بحروف صغيرة بالكامل عمداً (وليس userID كما كان
            // سابقاً) — عمود بحروف كابيتال وسط الاسم كان بيسبب مشكلة حقيقية
            // مع Oracle/OCI8: الـWHERE بالاسم القديم كان شغّال (Oracle
            // بيتعامل مع الأسماء غير المقتبسة case-insensitive)، لكن القراءة
            // بعد الجلب ($user->userID) كانت بترجع فارغة دايماً لأن مفتاح
            // المصفوفة الراجع من الدرايفر كان بحروف مختلفة الحالة عن الاسم
            // المكتوب بالضبط في الموديل — نفس العطل اللي ظهر في شاشة
            // المستخدمين (عمود "اسم مستخدم الدومين" فاضي رغم وجود القيمة
            // فعلياً في قاعدة البيانات).
            $table->string('domain_username', 100)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            // لا تُستخدم كلمة المرور المحلية فعلياً لتسجيل الدخول (المصادقة
            // تتم عبر LDAP)، لكنها مطلوبة لتوافق عقد Authenticatable
            // الخاص بـ Laravel، ويمكن استخدامها مستقبلاً كخطة بديلة.
            $table->string('password');
            // ربط المستخدم بالقطاع الإداري ليُستخدم في التحديد التلقائي
            // للقطاع في مسارات عمل: الحدث، متابعة الحدث، متابعة المؤشر.
            $table->unsignedInteger('sector_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->string('job_title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};