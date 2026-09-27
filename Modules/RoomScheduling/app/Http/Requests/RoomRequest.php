<?php

namespace Modules\RoomScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomId = $this->route("room");

        return [
            "code" => ["required", "string", "max:20", Rule::unique("rooms", "code")->ignore($roomId)],
            "name" => ["required", "string", "max:100"],
            "building" => ["nullable", "string", "max:100"],
            "floor" => ["nullable", "string", "max:20"],
            "capacity" => ["required", "integer", "min:1"],
            "type" => ["required", "in:lecture,laboratory,hybrid"],
            "is_active" => ["boolean"],
        ];
    }
}
