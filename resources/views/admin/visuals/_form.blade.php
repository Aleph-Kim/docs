{{-- $visual 은 edit 화면에서만 전달된다 --}}
@php($editing = isset($visual))

<form method="POST"
      action="{{ $editing ? route('admin.visuals.update', $visual) : route('admin.visuals.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="field">
        <label for="title">제목 *</label>
        <input type="text" name="title" id="title" value="{{ old('title', $visual->title ?? '') }}">
        @error('title') <div class="err">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="slug">슬러그</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $visual->slug ?? '') }}">
        <div class="hint">미입력 시 제목에서 자동 생성됩니다. 영문·숫자·- _ 만 사용.</div>
        @error('slug') <div class="err">{{ $message }}</div> @enderror
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
        @error('category_id') <div class="err">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="description">한 줄 설명</label>
        <input type="text" name="description" id="description" value="{{ old('description', $visual->description ?? '') }}">
        @error('description') <div class="err">{{ $message }}</div> @enderror
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
        @error('html_file') <div class="err">{{ $message }}</div> @enderror
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
        <button type="submit" class="btn btn-accent">{{ $editing ? '수정' : '등록' }}</button>
        <a href="{{ route('admin.visuals.index') }}" class="btn">취소</a>
    </div>
</form>
