<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoverUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'title' => ['required'],
            'description' => ['required'],
            'category_id' => ['required'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'mimes:jpg,bmp,png,jpeg', 'max:2048'], 
            'additional_images.*' => ['nullable', 'image', 'mimes:jpg,bmp,png,jpeg', 'max:2048'],
        ];
    }
}
