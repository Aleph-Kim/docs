<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('is_admin') === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:255', 'unique:visuals,slug'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'html_file' => ['required', 'file', 'mimetypes:text/html,text/plain', 'max:5120'],
        ];
    }
}
