@extends('layouts.instructor')

@section('title', 'الامتحانات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة الامتحانات</h6>
                    <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> إضافة امتحان جديد
                    </a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success mx-4 mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mx-4 mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الامتحان</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الدورة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">المدة (دقيقة)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">عدد الأسئلة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الحالة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الوقت</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">تاريخ الإنشاء</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizzes as $quiz)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $quiz->title }}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ Str::limit($quiz->description, 50) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $quiz->course->title }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $quiz->duration_minutes }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $quiz->questions_count }}</p>
                                        </td>
                                        <td>
                                            {!! $quiz->getStatusBadgeHtml() !!}
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $quiz->getTimeStatus() }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $quiz->created_at->format('Y-m-d') }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('instructor.quizzes.show', $quiz->quiz_id) }}" class="btn btn-info btn-sm me-2">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('instructor.quizzes.edit', $quiz->quiz_id) }}" class="btn btn-warning btn-sm me-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('instructor.quizzes.destroy', $quiz->quiz_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">لا توجد امتحانات حتى الآن</td>
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