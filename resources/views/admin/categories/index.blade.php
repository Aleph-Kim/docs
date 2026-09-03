@extends('layouts.app')

@section('title', '카테고리 관리')

@section('content')
    <div class="page-head">
        <h1>카테고리 관리</h1>
    </div>

    <div id="category-alert" class="flash" style="display:none; margin-bottom:16px;"></div>
    <div id="category-error" class="errors" style="display:none; margin-bottom:16px;"></div>

    <form id="create-category-form" method="POST" action="{{ route('admin.categories.store') }}" class="search" style="align-items: center;">
        @csrf
        <input type="text" name="name" id="new-cat-name" placeholder="카테고리 이름" required>
        <input type="text" name="slug" id="new-cat-slug" placeholder="슬러그 (선택)">
        <div class="color-input-wrap">
            <label class="color-swatch-btn" title="색상 선택">
                <input type="color" class="color-picker-hidden" value="#0f766e">
                <span class="color-swatch-dot" style="background-color: #0f766e;"></span>
            </label>
            <input type="text" name="color" id="new-cat-color" class="color-text-input" placeholder="#0f766e" maxlength="7">
        </div>
        <button type="submit" class="btn btn-accent" id="create-cat-btn">추가</button>
    </form>

    <div id="categories-container">
        @if ($categories->isEmpty())
            <div class="empty" id="empty-state">카테고리가 없습니다.</div>
        @else
            <div class="table-wrap">
                <table class="list" id="category-table">
                    <thead>
                        <tr>
                            <th>이름</th>
                            <th>슬러그</th>
                            <th class="nowrap">문서</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="category-tbody">
                        @foreach ($categories as $category)
                            <tr id="cat-row-{{ $category->id }}">
                                <td colspan="2">
                                    <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                                          class="row-actions category-update-form" style="align-items:center; justify-content: flex-start;">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $category->name }}" style="width:180px" required>
                                        <input type="text" name="slug" value="{{ $category->slug }}" style="width:160px">
                                        <div class="color-input-wrap">
                                            <label class="color-swatch-btn" title="색상 선택">
                                                <input type="color" class="color-picker-hidden" value="{{ $category->color ?: '#0f766e' }}">
                                                <span class="color-swatch-dot" style="background-color: {{ $category->color ?: '#0f766e' }};"></span>
                                            </label>
                                            <input type="text" name="color" class="color-text-input" value="{{ $category->color }}" placeholder="#0f766e" maxlength="7">
                                        </div>
                                        <button type="submit" class="btn">저장</button>
                                    </form>
                                </td>
                                <td class="nowrap">{{ $category->visuals_count }}</td>
                                <td class="actions">
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          class="category-delete-form">
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
            </div>
        @endif
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertEl = document.getElementById('category-alert');
    const errorEl = document.getElementById('category-error');
    const createForm = document.getElementById('create-category-form');

    function showAlert(msg) {
        errorEl.style.display = 'none';
        alertEl.innerText = msg;
        alertEl.style.display = 'block';
        setTimeout(() => { alertEl.style.display = 'none'; }, 3000);
    }

    function showError(msg) {
        alertEl.style.display = 'none';
        errorEl.innerText = msg;
        errorEl.style.display = 'block';
    }

    // 컬러 피커 및 Hex 텍스트 인풋 양방향 동기화
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('color-picker-hidden')) {
            const wrap = e.target.closest('.color-input-wrap');
            if (!wrap) return;
            const textInput = wrap.querySelector('.color-text-input');
            const dot = wrap.querySelector('.color-swatch-dot');
            if (textInput) textInput.value = e.target.value;
            if (dot) dot.style.backgroundColor = e.target.value;
        } else if (e.target.classList.contains('color-text-input')) {
            const wrap = e.target.closest('.color-input-wrap');
            if (!wrap) return;
            const picker = wrap.querySelector('.color-picker-hidden');
            const dot = wrap.querySelector('.color-swatch-dot');
            const val = e.target.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                if (picker) picker.value = val;
                if (dot) dot.style.backgroundColor = val;
            } else if (val === '') {
                if (picker) picker.value = '#0f766e';
                if (dot) dot.style.backgroundColor = '#0f766e';
            }
        }
    });

    // 카테고리 추가 비동기 전송
    createForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const submitBtn = document.getElementById('create-cat-btn');
        submitBtn.disabled = true;

        try {
            const formData = new FormData(createForm);
            const res = await fetch(createForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const json = await res.json();

            if (res.ok) {
                showAlert(json.message || '카테고리를 추가했습니다.');
                createForm.reset();
                const dot = createForm.querySelector('.color-swatch-dot');
                const picker = createForm.querySelector('.color-picker-hidden');
                if (dot) dot.style.backgroundColor = '#0f766e';
                if (picker) picker.value = '#0f766e';
                window.location.reload();
            } else {
                const msg = (json.errors && Object.values(json.errors).flat().join('\n')) || json.message || '오류가 발생했습니다.';
                showError(msg);
            }
        } catch (err) {
            showError('통신 중 오류가 발생했습니다.');
        } finally {
            submitBtn.disabled = false;
        }
    });

    // 카테고리 수정 비동기 전송
    document.addEventListener('submit', async function (e) {
        if (!e.target.classList.contains('category-update-form')) return;
        e.preventDefault();

        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const origText = btn.innerText;
        btn.disabled = true;
        btn.innerText = '...';

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
                showAlert(json.message || '카테고리를 수정했습니다.');
            } else {
                const msg = (json.errors && Object.values(json.errors).flat().join('\n')) || json.message || '오류가 발생했습니다.';
                showError(msg);
            }
        } catch (err) {
            showError('통신 중 오류가 발생했습니다.');
        } finally {
            btn.disabled = false;
            btn.innerText = origText;
        }
    });

    // 카테고리 삭제 비동기 전송
    document.addEventListener('submit', async function (e) {
        if (!e.target.classList.contains('category-delete-form')) return;
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
                showAlert(json.message || '카테고리를 삭제했습니다.');
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
