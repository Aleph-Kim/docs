@extends('layouts.app')

@section('title', '문서 수정')

@section('content')
    <div class="form-container">
        <div class="page-head">
            <h1>문서 수정</h1>
            <a href="{{ route('admin.visuals.index') }}" class="muted small">← 목록으로</a>
        </div>

        @include('admin.visuals._form', ['visual' => $visual, 'categories' => $categories])
    </div>
@endsection
