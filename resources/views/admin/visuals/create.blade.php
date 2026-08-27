@extends('layouts.app')

@section('title', '새 문서')

@section('content')
    <div class="page-head">
        <h1>새 문서</h1>
    </div>

    @include('admin.visuals._form', ['categories' => $categories])
@endsection
