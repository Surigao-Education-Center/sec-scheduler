@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Instructors</h3>
        <a href="{{ route('roomscheduling.instructors.create') }}" class="btn btn-primary btn-sm">+ Add Instructor</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Employee No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Active</th>
                <th style="width:140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($instructors as $instructor)
                <tr>
                    <td>{{ $instructor->employee_no }}</td>
                    <td>{{ $instructor->last_name }}, {{ $instructor->first_name }}</td>
                    <td>{{ $instructor->email }}</td>
                    <td>{{ $instructor->phone }}</td>
                    <td>{{ $instructor->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('roomscheduling.instructors.edit', $instructor) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('roomscheduling.instructors.destroy', $instructor) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this instructor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No instructors yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $instructors->links() }}
</div>
@endsection
