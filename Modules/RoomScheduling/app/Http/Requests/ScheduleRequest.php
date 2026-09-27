<?php

namespace Modules\RoomScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\RoomScheduling\Services\ScheduleConflictService;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate this in your host app if needed (e.g. via middleware/policies).
    }

    public function rules(): array
    {
        return [
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'instructor_id' => ['required', 'integer', 'exists:instructors,id'],
            'day' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'school_year' => ['required', 'string'],
            'semester' => ['required', 'string'],
            'allow_non_block_sectioning' => ['nullable', 'boolean'],
        ];
    }

    /**
     * After basic rules pass, run room/instructor/section overlap checks
     * and attach any conflicts as validation errors.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return; // don't check conflicts against malformed input
            }

            $allowNonBlockSectioning = (bool) ($this->boolean('allow_non_block_sectioning') ?? false);

            $conflicts = app(ScheduleConflictService::class)->findConflicts(
                $this->only([
                    'room_id', 'instructor_id', 'section_id',
                    'day', 'start_time', 'end_time', 'school_year', 'semester',
                ]),
                $this->route('schedule') ? (int) $this->route('schedule') : null,
                $allowNonBlockSectioning
            );

            foreach ($conflicts as $field => $message) {
                $validator->errors()->add($field, $message);
            }
        });
    }
}
