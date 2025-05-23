@extends('layouts.admin')

@section('title', 'إدارة الإنجازات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الإنجازات</h3>
                    <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> إضافة إنجاز جديد
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
                                    <th>النقاط</th>
                                    <th>الحالة</th>
                                    <th>عدد الطلاب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($achievements as $achievement)
                                    <tr>
                                        <td>{{ $achievement->id }}</td>
                                        <td>{{ $achievement->name }}</td>
                                        <td>{{ Str::limit($achievement->description, 50) }}</td>
                                        <td>
                                            <i class="fas fa-{{ $achievement->icon }}"></i>
                                        </td>
                                        <td>{{ $achievement->points }}</td>
                                        <td>
                                            <span class="badge {{ $achievement->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $achievement->is_active ? 'مفعل' : 'غير مفعل' }}
                                            </span>
                                        </td>
                                        <td>{{ $achievement->students()->count() }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.achievements.show', $achievement->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.achievements.edit', $achievement->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $achievement->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $achievement->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $achievement->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $achievement->id }}">تأكيد الحذف</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            هل أنت متأكد من رغبتك في حذف الإنجاز "{{ $achievement->name }}"؟
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                            <form action="{{ route('admin.achievements.destroy', $achievement->id) }}" method="POST">
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
                                        <td colspan="8" class="text-center">لا توجد إنجازات متاحة</td>
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
