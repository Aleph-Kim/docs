<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVisualRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:255', Rule::unique('visuals', 'slug')->ignore($this->route('visual'))],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            // 생략 시 기존 파일 유지
            'html_file' => ['nullable', 'file', 'mimetypes:text/html,text/plain', 'max:5120'],
        ];
    }
}
