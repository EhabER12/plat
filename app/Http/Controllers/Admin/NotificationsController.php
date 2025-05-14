<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * عرض قائمة الإشعارات.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // التحقق من التصفية
        $filter = $request->get('filter', 'all');
        $type = $request->get('type', null);
        $severity = (int) $request->get('severity', 0);
        
        // إنشاء استعلام أساسي
        $query = AdminNotification::with(['user']);
        
        // تطبيق الفلتر حسب حالة القراءة
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }
        
        // تطبيق الفلتر حسب النوع إذا تم تحديده
        if ($type) {
            $query->where('type', $type);
        }
        
        // تطبيق الفلتر حسب مستوى الخطورة إذا تم تحديده
        if ($severity > 0) {
            $query->where('severity', '>=', $severity);
        }
        
        // استرجاع النتائج مرتبة حسب الأحدث
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
        
        // استرجاع الإحصائيات
        $stats = [
            'total' => AdminNotification::count(),
            'unread' => AdminNotification::where('is_read', false)->count(),
            'flagged_content' => AdminNotification::where('type', 'flagged_content')->count(),
            'critical' => AdminNotification::where('severity', '>=', 4)->count(),
        ];
        
        // استرجاع قائمة الطلاب المشاركين في رسائل تم الإبلاغ عنها
        $flaggedUsers = User::whereIn('user_id', function($query) {
                $query->select('user_id')
                    ->from('admin_notifications')
                    ->where('type', 'flagged_content')
                    ->distinct();
            })
            ->limit(10)
            ->get();
        
        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'filter' => $filter,
            'type' => $type,
            'severity' => $severity,
            'flaggedUsers' => $flaggedUsers
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
        $notification = AdminNotification::with(['user'])->findOrFail($id);
        
        // تعليم الإشعار كمقروء إذا لم يكن مقروءًا
        if (!$notification->is_read) {
            $notification->markAsRead();
        }
        
        // جلب العنصر المرتبط بالإشعار إذا وجد
        $relatedItem = null;
        if ($notification->related_id && $notification->related_type) {
            if ($notification->related_type === DirectMessage::class) {
                // إذا كان الإشعار متعلق برسالة، جلب الرسالة مع المرسل والمستقبل
                $relatedItem = DirectMessage::with(['sender', 'receiver'])->find($notification->related_id);
            } elseif ($notification->related_type === User::class) {
                // إذا كان الإشعار متعلق بمستخدم، جلب المستخدم
                $relatedItem = User::find($notification->related_id);
            }
        }
        
        return view('admin.notifications.show', [
            'notification' => $notification,
            'relatedItem' => $relatedItem
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
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'تم تعليم الإشعار كمقروء');
    }
    
    /**
     * تعليم مجموعة من الإشعارات كمقروءة.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markMultipleAsRead(Request $request)
    {
        $ids = $request->input('notification_ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'لم يتم تحديد أي إشعارات');
        }
        
        AdminNotification::whereIn('id', $ids)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return redirect()->back()->with('success', 'تم تعليم الإشعارات المحددة كمقروءة');
    }
    
    /**
     * تعليم جميع الإشعارات كمقروءة.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        AdminNotification::where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
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
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * إنشاء إشعار اختبار للتأكد من عمل النظام
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTestNotification()
    {
        try {
            $notification = new AdminNotification();
            $notification->type = 'system_alert';
            $notification->content = 'هذا إشعار تجريبي للتأكد من عمل النظام - تم إنشاؤه في ' . now()->format('Y-m-d H:i:s');
            $notification->severity = 2;
            $notification->is_read = false;
            $notification->save();

            return redirect()->route('admin.notifications.index')
                ->with('success', 'تم إنشاء إشعار اختبار بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'حدث خطأ أثناء إنشاء الإشعار: ' . $e->getMessage());
        }
    }
}
