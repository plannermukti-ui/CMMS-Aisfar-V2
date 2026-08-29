<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('update', $this->route('message'));
    }

    public function rules(): array
    {
        return [
            'message' => 'sometimes|required|string|max:2000',
            'read_at' => 'sometimes|nullable|date',
        ];
    }
}
