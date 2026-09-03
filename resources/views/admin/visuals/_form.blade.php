{{-- $visual 은 edit 화면에서만 전달된다 --}}
@php
    $editing = isset($visual);
    $selectedCatId = (int) old('category_id', $editing ? ($visual->category_id ?? 0) : 0);
    $selectedCatName = '';
    foreach ($categories as $cat) {
        if ($cat->id === $selectedCatId) {
            $selectedCatName = $cat->name;
            break;
        }
    }
@endphp

<div class="form-card">
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
            <label for="title">제목 <span class="req">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $editing ? $visual->title : '') }}" placeholder="문서 제목을 입력하세요">
            <div class="err" id="err-title">@error('title') {{ $message }} @enderror</div>
        </div>

        <div class="form-row">
            <div class="field">
                <label for="category_id">카테고리 <span class="req">*</span></label>
                <input type="hidden" name="category_id" id="category_id" value="{{ $selectedCatId ?: '' }}">
                
                <div class="custom-select" id="category-custom-select">
                    <button type="button" class="custom-select-trigger" id="category-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span id="category-select-label">{{ $selectedCatName ?: '카테고리 선택' }}</span>
                        <span class="custom-select-arrow">▼</span>
                    </button>
                    <div class="custom-select-options" role="listbox" id="category-select-options">
                        <div class="custom-select-option {{ !$selectedCatId ? 'is-selected' : '' }}" data-value="">
                            카테고리 선택
                        </div>
                        @foreach ($categories as $category)
                            <div class="custom-select-option {{ $selectedCatId === $category->id ? 'is-selected' : '' }}"
                                 data-value="{{ $category->id }}"
                                 role="option">
                                {{ $category->name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="err" id="err-category_id">@error('category_id') {{ $message }} @enderror</div>
            </div>

            <div class="field">
                <label for="slug">슬러그</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $editing ? $visual->slug : '') }}" placeholder="예: my-document">
                <div class="hint">미입력 시 제목에서 자동 생성됩니다.</div>
                <div class="err" id="err-slug">@error('slug') {{ $message }} @enderror</div>
            </div>
        </div>

        <div class="field">
            <label for="description">한 줄 설명</label>
            <input type="text" name="description" id="description" value="{{ old('description', $editing ? $visual->description : '') }}" placeholder="문서에 대한 간단한 설명을 입력하세요">
            <div class="err" id="err-description">@error('description') {{ $message }} @enderror</div>
        </div>

        <div class="field">
            <label for="html_file">.html 파일 업로드 @unless ($editing) <span class="req">*</span>@endunless</label>
            <input type="file" name="html_file" id="html_file" accept=".html,text/html" style="display:none;">

            <div class="file-dropzone" id="file-dropzone" role="button" tabindex="0">
                <div class="file-dropzone-icon">📄</div>
                <div class="file-dropzone-text">클릭하거나 .html 파일을 여기로 드래그하세요</div>
                <div class="file-dropzone-hint">
                    @if ($editing)
                        새 파일을 올리면 교체되며, 비워두면 기존 파일이 유지됩니다.
                    @else
                        .html 형식의 단일 완성형 시각화 파일
                    @endif
                </div>
            </div>

            <div class="file-selected-box" id="file-selected-box">
                <div class="file-selected-info">
                    <span>선택된 파일:</span>
                    <span class="file-selected-name" id="file-selected-name"></span>
                    <span class="file-selected-size" id="file-selected-size"></span>
                </div>
                <button type="button" class="file-remove-btn" id="file-remove-btn" title="파일 선택 취소">&times;</button>
            </div>

            <div class="err" id="err-html_file">@error('html_file') {{ $message }} @enderror</div>
        </div>

        @if ($editing && $visual->file)
            <div class="field" style="margin-top: 24px;">
                <label>현재 저장된 HTML 미리보기</label>
                <div class="frame-wrap" style="border: 1px solid var(--line); border-radius: 4px; overflow: hidden;">
                    <iframe src="{{ $visual->file->url }}"
                            sandbox="allow-scripts allow-popups"
                            title="미리보기"
                            style="height: 480px;"></iframe>
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn btn-accent" id="submit-btn">{{ $editing ? '수정' : '등록' }}</button>
            <a href="{{ route('admin.visuals.index') }}" class="btn">취소</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('visual-form');
    const submitBtn = document.getElementById('submit-btn');
    const generalErr = document.getElementById('form-general-error');
    const fileInput = document.getElementById('html_file');
    const dropzone = document.getElementById('file-dropzone');
    const selectedBox = document.getElementById('file-selected-box');
    const selectedName = document.getElementById('file-selected-name');
    const selectedSize = document.getElementById('file-selected-size');
    const removeBtn = document.getElementById('file-remove-btn');

    // 커스텀 셀렉트 로직
    const customSelect = document.getElementById('category-custom-select');
    const selectTrigger = document.getElementById('category-select-trigger');
    const selectLabel = document.getElementById('category-select-label');
    const hiddenCategoryInput = document.getElementById('category_id');
    const selectOptions = document.getElementById('category-select-options');

    if (customSelect && selectTrigger && hiddenCategoryInput && selectOptions) {
        selectTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = customSelect.classList.toggle('is-open');
            selectTrigger.setAttribute('aria-expanded', isOpen);
        });

        selectOptions.querySelectorAll('.custom-select-option').forEach(option => {
            option.addEventListener('click', function (e) {
                e.stopPropagation();
                const val = this.getAttribute('data-value');
                const text = this.textContent.trim();

                hiddenCategoryInput.value = val;
                selectLabel.textContent = text;

                selectOptions.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('is-selected'));
                this.classList.add('is-selected');

                customSelect.classList.remove('is-open');
                selectTrigger.setAttribute('aria-expanded', 'false');
                selectTrigger.focus();
            });
        });

        document.addEventListener('click', function (e) {
            if (!customSelect.contains(e.target)) {
                customSelect.classList.remove('is-open');
                selectTrigger.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && customSelect.classList.contains('is-open')) {
                customSelect.classList.remove('is-open');
                selectTrigger.setAttribute('aria-expanded', 'false');
                selectTrigger.focus();
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function updateFileDisplay() {
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            selectedName.textContent = file.name;
            selectedSize.textContent = '(' + formatFileSize(file.size) + ')';
            selectedBox.style.display = 'flex';
        } else {
            selectedBox.style.display = 'none';
            selectedName.textContent = '';
            selectedSize.textContent = '';
        }
    }

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt.files && dt.files.length > 0) {
                fileInput.files = dt.files;
                updateFileDisplay();
            }
        });

        fileInput.addEventListener('change', updateFileDisplay);

        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.value = '';
                updateFileDisplay();
            });
        }
    }

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
                await window.AppDialog.alert(data.message || '문서를 저장했습니다.');
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
                await window.AppDialog.alert('입력 항목을 확인해주세요.', '오류');
            } else {
                const errorMsg = data.message || '오류가 발생했습니다.';
                generalErr.innerHTML = errorMsg;
                generalErr.style.display = 'block';
                await window.AppDialog.alert(errorMsg, '오류');
            }
        } catch (err) {
            generalErr.innerHTML = '네트워크 통신 중 오류가 발생했습니다.';
            generalErr.style.display = 'block';
            await window.AppDialog.alert('네트워크 통신 중 오류가 발생했습니다.', '오류');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        }
    });
});
</script>
