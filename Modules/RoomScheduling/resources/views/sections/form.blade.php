@extends('layouts.app')

@section('content')
<div class="page-wrap form-shell">
    <div class="page-heading" style="margin-bottom: 18px;">
        <div>
            <div class="eyebrow">Section setup</div>
            <h1>{{ $section->exists ? 'Edit Section' : 'Add Section' }}</h1>
            <p>{{ $section->exists ? 'Update this section profile.' : 'Create a new section for an academic subject.' }}</p>
        </div>
    </div>

    <div class="form-panel">
        @if ($errors->any())
            <div class="notice notice-error" style="border-color: #efc3b5; background: #fff5f1; color: #924c3b; margin-bottom: 20px;">
                <ul class="mb-0" style="padding-left: 18px; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $section->exists ? route('roomscheduling.sections.update', $section) : route('roomscheduling.sections.store') }}" class="entity-form">
            @csrf
            @if ($section->exists) @method('PUT') @endif

            <div class="field-group">
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">— Select subject —</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $section->subject_id) == $subject->id)>
                            {{ $subject->code }} - {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label for="section_code">Section code</label>
                <input type="text" id="section_code" name="section_code" value="{{ old('section_code', $section->section_code) }}" placeholder="e.g. BSCS-1A" required>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="school_year">School year</label>
                    <input type="text" id="school_year" name="school_year" value="{{ old('school_year', $section->school_year) }}" placeholder="e.g. 2026-2027" required>
                </div>
                <div class="field-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        @foreach (config('roomscheduling.semesters') as $semester)
                            <option value="{{ $semester }}" @selected(old('semester', $section->semester) === $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field-group">
                <label for="max_students">Max students</label>
                <input type="number" id="max_students" name="max_students" value="{{ old('max_students', $section->max_students ?? 40) }}" min="1" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="button">Save section</button>
                <a href="{{ route('roomscheduling.sections.index') }}" class="button button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
