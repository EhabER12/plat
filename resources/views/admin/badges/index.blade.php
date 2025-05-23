@extends('layouts.admin')

@section('title', 'إدارة الشارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الشارات</h3>
                    <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> إضافة شارة جديدة
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>الأيقونة</th>
                                    <th>المستوى</th>
                                    <th>الحالة</th>
                                    <th>عدد الطلاب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($badges as $badge)
                                    <tr>
                                        <td>{{ $badge->id }}</td>
                                        <td>{{ $badge->name }}</td>
                                        <td>{{ Str::limit($badge->description, 50) }}</td>
                                        <td>
                                            <i class="fas fa-{{ $badge->icon == 'explore' ? 'compass' : ($badge->icon == 'persistence' ? 'tasks' : ($badge->icon == 'streak' ? 'fire' : ($badge->icon == 'star' ? 'star' : ($badge->icon == 'perfect' ? 'award' : $badge->icon)))) }}"></i>
                                        </td>
                                        <td>{{ $badge->level }}</td>
                                        <td>
                                            <span class="badge {{ $badge->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $badge->is_active ? 'مفعل' : 'غير مفعل' }}
                                            </span>
                                        </td>
                                        <td>{{ $badge->students()->count() }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.badges.show', $badge->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.badges.edit', $badge->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $badge->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $badge->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $badge->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $badge->id }}">تأكيد الحذف</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            هل أنت متأكد من رغبتك في حذف الشارة "{{ $badge->name }}"؟
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                            <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">حذف</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد شارات متاحة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
