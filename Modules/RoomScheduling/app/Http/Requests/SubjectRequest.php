<?php

namespace Modules\RoomScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route("subject");

        return [
            "code" => ["required", "string", "max:20", Rule::unique("subjects", "code")->ignore($subjectId)],
            "name" => ["required", "string", "max:150"],
            "units" => ["required", "integer", "min:1", "max:12"],
            "lecture_hours" => ["required", "integer", "min:0", "max:20"],
            "lab_hours" => ["required", "integer", "min:0", "max:20"],
            "description" => ["nullable", "string"],
            "is_active" => ["boolean"],
        ];
    }
}
