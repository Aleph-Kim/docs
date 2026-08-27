<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('is_admin') === true;
    }

    /**
     * .html 파일이 업로드된 경우 저장하지 않고 내용만 읽어 html 입력으로 대체한다.
     */
    protected function prepareForValidation(): void
    {
        if ($this->hasFile('html_file') && $this->file('html_file')->isValid()) {
            $this->merge([
                'html' => file_get_contents($this->file('html_file')->getRealPath()),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:255', 'unique:visuals,slug'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'html_file' => ['nullable', 'file', 'mimetypes:text/html,text/plain', 'max:5120'],
            'html' => ['required', 'string'],
        ];
    }
}
