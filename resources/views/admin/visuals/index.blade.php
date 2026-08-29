@extends('layouts.app')

@section('title', '문서 관리')

@section('content')
    <div class="page-head">
        <h1>문서 관리</h1>
        <a href="{{ route('admin.visuals.create') }}" class="btn btn-accent">새 문서</a>
    </div>

    <div id="visual-alert" class="flash" style="display:none; margin-bottom:16px;"></div>
    <div id="visual-error" class="errors" style="display:none; margin-bottom:16px;"></div>

    @if ($visuals->isEmpty())
        <div class="empty">등록된 문서가 없습니다.</div>
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
                    <tr id="visual-row-{{ $visual->id }}">
                        <td>{{ $visual->title }}</td>
                        <td>{{ $visual->category->name }}</td>
                        <td>{{ $visual->slug }}</td>
                        <td>{{ $visual->created_at->format('Y-m-d') }}</td>
                        <td>
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

        <div class="pagination-wrap">
            {{ $visuals->links() }}
        </div>
    @endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertEl = document.getElementById('visual-alert');
    const errorEl = document.getElementById('visual-error');

    function showAlert(msg) {
        if (errorEl) errorEl.style.display = 'none';
        if (alertEl) {
            alertEl.innerText = msg;
            alertEl.style.display = 'block';
            setTimeout(() => { alertEl.style.display = 'none'; }, 3000);
        }
    }

    function showError(msg) {
        if (alertEl) alertEl.style.display = 'none';
        if (errorEl) {
            errorEl.innerText = msg;
            errorEl.style.display = 'block';
        }
    }

    document.addEventListener('submit', async function (e) {
        if (!e.target.classList.contains('visual-delete-form')) return;
        e.preventDefault();

        if (!confirm('삭제하시겠습니까?')) return;

        const form = e.target;
        const row = form.closest('tr');
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
                showAlert(json.message || '문서를 삭제했습니다.');
                row.remove();
            } else {
                showError(json.message || '삭제에 실패했습니다.');
                btn.disabled = false;
            }
        } catch (err) {
            showError('통신 중 오류가 발생했습니다.');
            btn.disabled = false;
        }
    });
});
</script>
@endsection
