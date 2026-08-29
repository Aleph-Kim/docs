{{-- $visual 은 edit 화면에서만 전달된다 --}}
@php($editing = isset($visual))

<form id="visual-form"
      method="POST"
      action="{{ $editing ? route('admin.visuals.update', $visual) : route('admin.visuals.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div id="form-general-error" class="errors" style="display:none; margin-bottom:16px;"></div>

    <div class="field">
        <label for="title">제목 *</label>
        <input type="text" name="title" id="title" value="{{ old('title', $visual->title ?? '') }}">
        <div class="err" id="err-title">@error('title') {{ $message }} @enderror</div>
    </div>

    <div class="field">
        <label for="slug">슬러그</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $visual->slug ?? '') }}">
        <div class="hint">미입력 시 제목에서 자동 생성됩니다. 영문·숫자·- _ 만 사용.</div>
        <div class="err" id="err-slug">@error('slug') {{ $message }} @enderror</div>
    </div>

    <div class="field">
        <label for="category_id">카테고리 *</label>
        <select name="category_id" id="category_id">
            <option value="">선택</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((int) old('category_id', $visual->category_id ?? 0) === $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <div class="err" id="err-category_id">@error('category_id') {{ $message }} @enderror</div>
    </div>

    <div class="field">
        <label for="description">한 줄 설명</label>
        <input type="text" name="description" id="description" value="{{ old('description', $visual->description ?? '') }}">
        <div class="err" id="err-description">@error('description') {{ $message }} @enderror</div>
    </div>

    <div class="field">
        <label for="html_file">.html 파일 업로드 @unless ($editing) *@endunless</label>
        <input type="file" name="html_file" id="html_file" accept=".html,text/html">
        <div class="hint">
            @if ($editing)
                새 파일을 올리면 교체됩니다. 비워두면 기존 파일이 유지됩니다.
            @else
                업로드한 파일이 저장되고 상세 페이지에 표시됩니다.
            @endif
        </div>
        <div class="err" id="err-html_file">@error('html_file') {{ $message }} @enderror</div>
    </div>

    @if ($editing && $visual->file)
        <div class="field">
            <label>현재 저장된 HTML 미리보기</label>
            <div class="frame-wrap">
                <iframe src="{{ $visual->file->url }}"
                        sandbox="allow-scripts allow-popups"
                        title="미리보기"></iframe>
            </div>
        </div>
    @endif

    <div class="form-actions">
        <button type="submit" class="btn btn-accent" id="submit-btn">{{ $editing ? '수정' : '등록' }}</button>
        <a href="{{ route('admin.visuals.index') }}" class="btn">취소</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('visual-form');
    const submitBtn = document.getElementById('submit-btn');
    const generalErr = document.getElementById('form-general-error');

    function clearErrors() {
        generalErr.style.display = 'none';
        generalErr.innerHTML = '';
        document.querySelectorAll('.err').forEach(el => el.innerHTML = '');
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        submitBtn.disabled = true;
        const originalText = submitBtn.innerText;
        submitBtn.innerText = '저장 중...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = "{{ route('admin.visuals.index') }}";
                return;
            }

            if (response.status === 422 && data.errors) {
                for (const [field, messages] of Object.entries(data.errors)) {
                    const errEl = document.getElementById('err-' + field);
                    if (errEl) {
                        errEl.innerHTML = messages.join('<br>');
                    }
                }
                generalErr.innerHTML = '입력 항목을 확인해주세요.';
                generalErr.style.display = 'block';
            } else {
                generalErr.innerHTML = data.message || '오류가 발생했습니다.';
                generalErr.style.display = 'block';
            }
        } catch (err) {
            generalErr.innerHTML = '네트워크 통신 중 오류가 발생했습니다.';
            generalErr.style.display = 'block';
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        }
    });
});
</script>
