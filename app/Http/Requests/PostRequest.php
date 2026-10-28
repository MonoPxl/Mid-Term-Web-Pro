<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    /**
     * Determine whether the user is allowed to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'type' => ['required', Rule::enum(PostType::class)],
            'body' => ['required', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([PostStatus::Draft->value, PostStatus::Published->value])],
            'event_starts_at' => ['nullable', 'required_if:type,event', 'date'],
            'event_ends_at' => ['nullable', 'required_if:type,event', 'date', 'after_or_equal:event_starts_at'],
            'event_location' => ['nullable', 'required_if:type,event', 'string', 'max:255'],
        ];
    }
}
