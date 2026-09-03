@extends('layouts.app')

@section('title', '문서 관리')

@section('content')
    <div class="page-head">
        <h1>문서 관리</h1>
        <a href="{{ route('admin.visuals.create') }}" class="btn btn-accent">새 문서</a>
    </div>

    <form method="GET" action="{{ route('admin.visuals.index') }}" class="search">
        <input type="text" name="q" value="{{ $keyword }}" placeholder="제목·설명 검색">
        <button type="submit" class="btn">검색</button>
        @if ($keyword !== '')
            <a href="{{ route('admin.visuals.index') }}" class="btn">초기화</a>
        @endif
    </form>

    @if ($visuals->isEmpty())
        @if ($keyword !== '')
            <div class="empty">검색 결과가 없습니다.</div>
        @else
            <div class="empty">등록된 문서가 없습니다.</div>
        @endif
    @else
        <div class="table-wrap">
            <table class="list">
                <thead>
                    <tr>
                        <th>제목</th>
                        <th class="nowrap">카테고리</th>
                        <th>슬러그</th>
                        <th class="nowrap">등록일</th>
                        <th class="actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visuals as $visual)
                        <tr id="visual-row-{{ $visual->id }}">
                            <td class="title">{{ $visual->title }}</td>
                            <td class="nowrap">{{ $visual->category->name }}</td>
                            <td class="slug">{{ $visual->slug }}</td>
                            <td class="nowrap">{{ $visual->created_at->format('Y-m-d') }}</td>
                            <td class="actions">
                                <div class="row-actions">
                                    <a href="{{ route('visuals.show', $visual->slug) }}" target="_blank" rel="noopener" class="btn">보기</a>
                                    <a href="{{ route('admin.visuals.edit', $visual) }}" class="btn">수정</a>
                                    <form method="POST" action="{{ route('admin.visuals.destroy', $visual) }}"
                                          class="visual-delete-form">
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
        </div>

        <div class="pagination-wrap">
            {{ $visuals->links() }}
        </div>
    @endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    function showAlert(msg) {
        return window.AppDialog.alert(msg);
    }

    function showError(msg) {
        return window.AppDialog.alert(msg, '오류');
    }

    document.addEventListener('submit', async function (e) {
        if (!e.target.classList.contains('visual-delete-form')) return;
        e.preventDefault();

        const form = e.target;
        const row = form.closest('tr');
        const title = row?.querySelector('.title')?.textContent.trim() || '문서';

        const confirmed = await window.AppDialog.confirm(`'${title}' 문서를 삭제하시겠습니까?`, '문서 삭제');
        if (!confirmed) return;

        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;

        try {
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const json = await res.json();

            if (res.ok) {
                await showAlert(json.message || '문서를 삭제했습니다.');
                row.remove();
            } else {
                await showError(json.message || '삭제에 실패했습니다.');
                btn.disabled = false;
            }
        } catch (err) {
            await showError('통신 중 오류가 발생했습니다.');
            btn.disabled = false;
        }
    });
});
</script>
@endsection
