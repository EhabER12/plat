<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * عرض قائمة الإشعارات للطالب.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // التحقق من التصفية
        $filter = $request->get('filter', 'all');

        // إنشاء استعلام أساسي
        $query = Notification::where('user_id', $user->user_id);

        // تطبيق الفلتر حسب حالة القراءة
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // استرجاع النتائج مرتبة حسب الأحدث
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // استرجاع الإحصائيات
        $stats = [
            'total' => Notification::where('user_id', $user->user_id)->count(),
            'unread' => Notification::where('user_id', $user->user_id)->whereNull('read_at')->count(),
        ];

        return view('student.notifications.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'filter' => $filter
        ]);
    }

    /**
     * عرض تفاصيل إشعار معين.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->user_id)
            ->where('notification_id', $id)
            ->firstOrFail();

        // تعليم الإشعار كمقروء إذا لم يكن مقروءًا
        if (!$notification->isRead()) {
            $notification->markAsRead();
        }

        return view('student.notifications.show', [
            'notification' => $notification
        ]);
    }

    /**
     * تعليم إشعار كمقروء.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->user_id)
            ->where('notification_id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->back()->with('success', 'تم تعليم الإشعار كمقروء');
    }

    /**
     * تعليم جميع الإشعارات كمقروءة.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'تم تعليم جميع الإشعارات كمقروءة');
    }

    /**
     * حذف إشعار.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->user_id)
            ->where('notification_id', $id)
            ->firstOrFail();

        $notification->delete();

        return redirect()->route('student.notifications.index')->with('success', 'تم حذف الإشعار بنجاح');
    }
}
