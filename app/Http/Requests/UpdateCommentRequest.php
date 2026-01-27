<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CommentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commenter_name' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:500'],

            'status' => ['required', Rule::enum(CommentStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
