@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <h3>{{ $subject->exists ? 'Edit Subject' : 'Add Subject' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $subject->exists ? route('roomscheduling.subjects.update', $subject) : route('roomscheduling.subjects.store') }}">
        @csrf
        @if ($subject->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code) }}" placeholder="e.g. CS101" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Units</label>
                <input type="number" name="units" class="form-control" value="{{ old('units', $subject->units ?? 3) }}" min="1" max="12" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Lecture Hours</label>
                <input type="number" name="lecture_hours" class="form-control" value="{{ old('lecture_hours', $subject->lecture_hours ?? 3) }}" min="0" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Lab Hours</label>
                <input type="number" name="lab_hours" class="form-control" value="{{ old('lab_hours', $subject->lab_hours ?? 0) }}" min="0" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $subject->description) }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $subject->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Save Subject</button>
        <a href="{{ route('roomscheduling.subjects.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
