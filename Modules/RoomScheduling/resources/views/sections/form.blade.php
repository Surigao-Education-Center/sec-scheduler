@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <h3>{{ $section->exists ? 'Edit Section' : 'Add Section' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $section->exists ? route('roomscheduling.sections.update', $section) : route('roomscheduling.sections.store') }}">
        @csrf
        @if ($section->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <select name="subject_id" class="form-select" required>
                <option value="">— Select subject —</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(old('subject_id', $section->subject_id) == $subject->id)>
                        {{ $subject->code }} - {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Section Code</label>
            <input type="text" name="section_code" class="form-control" value="{{ old('section_code', $section->section_code) }}" placeholder="e.g. BSCS-1A" required>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">School Year</label>
                <input type="text" name="school_year" class="form-control" value="{{ old('school_year', $section->school_year) }}" placeholder="e.g. 2026-2027" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" required>
                    @foreach (config('roomscheduling.semesters') as $semester)
                        <option value="{{ $semester }}" @selected(old('semester', $section->semester) === $semester)>{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Max Students</label>
            <input type="number" name="max_students" class="form-control" value="{{ old('max_students', $section->max_students ?? 40) }}" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Section</button>
        <a href="{{ route('roomscheduling.sections.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
