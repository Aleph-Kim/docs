@extends('layouts.app')

@section('title', '카테고리 관리')

@section('content')
    <div class="page-head">
        <h1>카테고리 관리</h1>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="search">
        @csrf
        <input type="text" name="name" placeholder="카테고리 이름" value="{{ old('name') }}">
        <input type="text" name="slug" placeholder="슬러그 (선택)" value="{{ old('slug') }}">
        <button type="submit" class="btn btn-accent">추가</button>
    </form>

    @if ($categories->isEmpty())
        <div class="empty">카테고리가 없습니다.</div>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>슬러그</th>
                    <th>문서</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td colspan="2">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                                  class="row-actions" style="align-items:center">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" style="width:180px">
                                <input type="text" name="slug" value="{{ $category->slug }}" style="width:160px">
                                <button type="submit" class="btn">저장</button>
                            </form>
                        </td>
                        <td>{{ $category->visuals_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        @disabled($category->visuals_count > 0)>삭제</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
