@extends('layouts.app')

@section('content')
<div class="page-wrap form-shell">
    <div class="page-heading" style="margin-bottom: 18px;">
        <div>
            <div class="eyebrow">Curriculum setup</div>
            <h1>{{ $subject->exists ? 'Edit Subject' : 'Add Subject' }}</h1>
            <p>{{ $subject->exists ? 'Update the course details below.' : 'Add a new subject to the academic catalog.' }}</p>
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

        <form method="POST" action="{{ $subject->exists ? route('roomscheduling.subjects.update', $subject) : route('roomscheduling.subjects.store') }}" class="entity-form">
            @csrf
            @if ($subject->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field-group">
                    <label for="code">Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $subject->code) }}" placeholder="e.g. CS101" required>
                </div>
                <div class="field-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="units">Units</label>
                    <input type="number" id="units" name="units" value="{{ old('units', $subject->units ?? 3) }}" min="1" max="12" required>
                </div>
                <div class="field-group">
                    <label for="lecture_hours">Lecture hours</label>
                    <input type="number" id="lecture_hours" name="lecture_hours" value="{{ old('lecture_hours', $subject->lecture_hours ?? 3) }}" min="0" required>
                </div>
                <div class="field-group">
                    <label for="lab_hours">Lab hours</label>
                    <input type="number" id="lab_hours" name="lab_hours" value="{{ old('lab_hours', $subject->lab_hours ?? 0) }}" min="0" required>
                </div>
            </div>

            <div class="field-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $subject->description) }}</textarea>
            </div>

            <label class="switch-field" for="is_active">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $subject->is_active ?? true))>
                Active subject
            </label>

            <div class="form-actions">
                <button type="submit" class="button">Save subject</button>
                <a href="{{ route('roomscheduling.subjects.index') }}" class="button button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
