@extends('layouts.app')

@section('title', '새 문서')

@section('content')
    <div class="form-container">
        <div class="page-head">
            <h1>새 문서</h1>
            <a href="{{ route('admin.visuals.index') }}" class="muted small">← 목록으로</a>
        </div>

        @include('admin.visuals._form', ['categories' => $categories])
    </div>
@endsection
