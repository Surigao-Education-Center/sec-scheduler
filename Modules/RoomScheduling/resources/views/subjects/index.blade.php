@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Curriculum</div>
            <h1>Subjects</h1>
            <p>Review the academic courses offered, workload values, and instructional mix.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.subjects.create') }}" class="button">+ Add subject</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="eyebrow">Total subjects</div>
            <strong>{{ $subjects->total() }}</strong>
            <span>Offerings in catalog</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Active</div>
            <strong>{{ $subjects->where('is_active', true)->count() }}</strong>
            <span>Available to schedule</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Unit load</div>
            <strong>{{ $subjects->sum('units') }}</strong>
            <span>Total credit units</span>
        </div>
    </div>

    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Units</th>
                        <th>Lec / Lab</th>
                        <th>Status</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <td><strong>{{ $subject->code }}</strong></td>
                            <td>
                                <span class="entity-name">{{ $subject->name }}</span>
                                <span class="table-subtext">{{ Str::limit($subject->description ?? 'No description', 60) }}</span>
                            </td>
                            <td>{{ $subject->units }}</td>
                            <td>{{ $subject->lecture_hours }} / {{ $subject->lab_hours }}</td>
                            <td>
                                <span class="status-pill {{ $subject->is_active ? 'active' : 'inactive' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="action-group">
                                <a href="{{ route('roomscheduling.subjects.edit', $subject) }}" class="button button-small button-secondary">Edit</a>
                                <form action="{{ route('roomscheduling.subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Delete this subject?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-small button-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No subjects yet. Add a course to build the curriculum list.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">{{ $subjects->links() }}</div>
</div>
@endsection
