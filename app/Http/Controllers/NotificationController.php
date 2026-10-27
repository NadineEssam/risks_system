<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** كل إشعارات المستخدم */
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /** فتح إشعار: يتعلّم كمقروء ويروح للرابط */
    public function read(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // المسار المخزّن + عنوان الجهاز الحالي (يشتغل صح محلياً وعلى السيرفر)
        // (الإشعارات القديمة اللي متخزنة بعنوان كامل بنحوّلها لمسار برضه)
        $path = \App\Notifications\SystemNotification::relativePath($notification->data['url'] ?? null);

        return redirect($path ? url($path) : route('notifications.index'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'تم تحديد كل الإشعارات كمقروءة.');
    }
}