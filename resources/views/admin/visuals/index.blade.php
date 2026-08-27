@extends('layouts.app')

@section('title', '시각화 관리')

@section('content')
    <div class="page-head">
        <h1>시각화 관리</h1>
        <a href="{{ route('admin.visuals.create') }}" class="btn btn-accent">새 시각화</a>
    </div>

    @if ($visuals->isEmpty())
        <div class="empty">등록된 시각화가 없습니다.</div>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th>제목</th>
                    <th>카테고리</th>
                    <th>슬러그</th>
                    <th>등록일</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visuals as $visual)
                    <tr>
                        <td>{{ $visual->title }}</td>
                        <td>{{ $visual->category->name }}</td>
                        <td>{{ $visual->slug }}</td>
                        <td>{{ $visual->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('visuals.show', $visual->slug) }}" target="_blank" rel="noopener" class="btn">보기</a>
                                <a href="{{ route('admin.visuals.edit', $visual) }}" class="btn">수정</a>
                                <form method="POST" action="{{ route('admin.visuals.destroy', $visual) }}"
                                      onsubmit="return confirm('삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">삭제</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            {{ $visuals->links() }}
        </div>
    @endif
@endsection
