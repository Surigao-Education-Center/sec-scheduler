<?php

namespace Modules\RoomScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instructorId = $this->route("instructor");

        return [
            "user_id" => ["nullable", "integer"],
            "employee_no" => ["nullable", "string", "max:50", Rule::unique("instructors", "employee_no")->ignore($instructorId)],
            "first_name" => ["required", "string", "max:100"],
            "last_name" => ["required", "string", "max:100"],
            "email" => ["nullable", "email", "max:150"],
            "phone" => ["nullable", "string", "max:30"],
            "is_active" => ["boolean"],
        ];
    }
}
