@extends('layouts.app')

@section('title', '새 시각화')

@section('content')
    <div class="page-head">
        <h1>새 시각화</h1>
    </div>

    @include('admin.visuals._form', ['categories' => $categories])
@endsection
