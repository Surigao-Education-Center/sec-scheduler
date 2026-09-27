<?php

namespace Modules\RoomScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "subject_id" => ["required", "integer", "exists:subjects,id"],
            "section_code" => ["required", "string", "max:50"],
            "school_year" => ["required", "string", "max:20"],
            "semester" => ["required", "string", "max:30"],
            "max_students" => ["required", "integer", "min:1"],
        ];
    }
}
