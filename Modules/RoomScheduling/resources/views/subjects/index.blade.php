@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Subjects</h3>
        <a href="{{ route('roomscheduling.subjects.create') }}" class="btn btn-primary btn-sm">+ Add Subject</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Units</th>
                <th>Lec / Lab hrs</th>
                <th>Active</th>
                <th style="width:140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subjects as $subject)
                <tr>
                    <td>{{ $subject->code }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>{{ $subject->units }}</td>
                    <td>{{ $subject->lecture_hours }} / {{ $subject->lab_hours }}</td>
                    <td>{{ $subject->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('roomscheduling.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('roomscheduling.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No subjects yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $subjects->links() }}
</div>
@endsection
