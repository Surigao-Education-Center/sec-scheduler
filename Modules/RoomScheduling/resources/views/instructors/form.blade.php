@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <h3>{{ $instructor->exists ? 'Edit Instructor' : 'Add Instructor' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $instructor->exists ? route('roomscheduling.instructors.update', $instructor) : route('roomscheduling.instructors.store') }}">
        @csrf
        @if ($instructor->exists) @method('PUT') @endif

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $instructor->first_name) }}" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $instructor->last_name) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Employee No.</label>
            <input type="text" name="employee_no" class="form-control" value="{{ old('employee_no', $instructor->employee_no) }}">
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $instructor->email) }}">
            </div>
            <div class="col mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $instructor->phone) }}">
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $instructor->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Save Instructor</button>
        <a href="{{ route('roomscheduling.instructors.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
