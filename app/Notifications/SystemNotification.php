<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار عام للنظام: عنوان + نص + رابط + أيقونة ولون.
 * بيتخزن في جدول notifications (الجرس)، وبيتبعت إيميل لو NOTIFY_MAIL=true.
 */
class SystemNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public string $icon = 'bi-bell',
        public string $color = 'primary',
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('app.notify_mail') && ! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => self::relativePath($this->url),
            'icon'  => $this->icon,
            'color' => $this->color,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('نظام إدارة المخاطر — '.$this->title)
            ->greeting('السيد/ة '.$notifiable->name)
            ->line($this->body);

        if ($this->url) {
            $mail->action('عرض التفاصيل', $this->url);
        }

        return $mail->salutation('نظام إدارة المخاطر التشغيلية');
    }

    /**
     * نخزّن مسار الصفحة بس (/indicators/1/followups) — من غير الدومين —
     * عشان الإشعار اللي اتعمل من اللابتوب يفتح صح على السيرفر والعكس.
     */
    public static function relativePath(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $path  = parse_url($url, PHP_URL_PATH) ?? '/';
        $query = parse_url($url, PHP_URL_QUERY);

        // نشيل المسار الفرعي (زي /risk_management) لو موجود في APP_URL
        $base = rtrim((string) parse_url(config('app.url'), PHP_URL_PATH), '/');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        return $path.($query ? '?'.$query : '');
    }
}