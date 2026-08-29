<?php

namespace App\Http\Requests;

use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', Message::class);
    }

    public function rules(): array
    {
        return [
            'receiver_id' => 'required|uuid|exists:users,id',
            'message' => 'required|string|max:2000',
        ];
    }
}
