@extends('layouts.app')

@section('content')
<div class="page-wrap form-shell">
    <div class="page-heading" style="margin-bottom: 18px;">
        <div>
            <div class="eyebrow">Instructor setup</div>
            <h1>{{ $instructor->exists ? 'Edit Instructor' : 'Add Instructor' }}</h1>
            <p>{{ $instructor->exists ? 'Update the faculty member profile.' : 'Create a new instructor record for assignment planning.' }}</p>
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

        <form method="POST" action="{{ $instructor->exists ? route('roomscheduling.instructors.update', $instructor) : route('roomscheduling.instructors.store') }}" class="entity-form">
            @csrf
            @if ($instructor->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field-group">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $instructor->first_name) }}" required>
                </div>
                <div class="field-group">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $instructor->last_name) }}" required>
                </div>
            </div>

            <div class="field-group">
                <label for="employee_no">Employee no.</label>
                <input type="text" id="employee_no" name="employee_no" value="{{ old('employee_no', $instructor->employee_no) }}">
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $instructor->email) }}">
                </div>
                <div class="field-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $instructor->phone) }}">
                </div>
            </div>

            <label class="switch-field" for="is_active">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $instructor->is_active ?? true))>
                Active instructor
            </label>

            <div class="form-actions">
                <button type="submit" class="button">Save instructor</button>
                <a href="{{ route('roomscheduling.instructors.index') }}" class="button button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
