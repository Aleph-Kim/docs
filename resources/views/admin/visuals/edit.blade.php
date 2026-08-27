@extends('layouts.app')

@section('title', '문서 수정')

@section('content')
    <div class="page-head">
        <h1>문서 수정</h1>
    </div>

    @include('admin.visuals._form', ['visual' => $visual, 'categories' => $categories])
@endsection
