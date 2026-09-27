@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Sections</h3>
        <a href="{{ route('roomscheduling.sections.create') }}" class="btn btn-primary btn-sm">+ Add Section</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Section</th>
                <th>Subject</th>
                <th>School Year</th>
                <th>Semester</th>
                <th>Max Students</th>
                <th style="width:140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sections as $section)
                <tr>
                    <td>{{ $section->section_code }}</td>
                    <td>{{ $section->subject->code ?? '—' }} - {{ $section->subject->name ?? '' }}</td>
                    <td>{{ $section->school_year }}</td>
                    <td>{{ $section->semester }}</td>
                    <td>{{ $section->max_students }}</td>
                    <td>
                        <a href="{{ route('roomscheduling.sections.edit', $section) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('roomscheduling.sections.destroy', $section) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this section?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No sections yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $sections->links() }}
</div>
@endsection
