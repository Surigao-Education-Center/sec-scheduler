@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Faculty roster</div>
            <h1>Instructors</h1>
            <p>View faculty records, contact details, and assignment readiness in one place.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.instructors.create') }}" class="button">+ Add instructor</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="eyebrow">Faculty</div>
            <strong>{{ $instructors->total() }}</strong>
            <span>Personnel on record</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Available</div>
            <strong>{{ $instructors->where('is_active', true)->count() }}</strong>
            <span>Ready for load assignment</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Coverage</div>
            <strong>{{ $instructors->where('is_active', true)->count() > 0 ? '100%' : '0%' }}</strong>
            <span>Active teaching roster</span>
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
                        <th>Employee no.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($instructors as $instructor)
                        <tr>
                            <td><strong>{{ $instructor->employee_no ?? '—' }}</strong></td>
                            <td>
                                <span class="entity-name">{{ $instructor->last_name }}, {{ $instructor->first_name }}</span>
                                <span class="table-subtext">Faculty profile</span>
                            </td>
                            <td>{{ $instructor->email ?? '—' }}</td>
                            <td>{{ $instructor->phone ?? '—' }}</td>
                            <td>
                                <span class="status-pill {{ $instructor->is_active ? 'active' : 'inactive' }}">
                                    {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="action-group">
                                <a href="{{ route('roomscheduling.instructors.edit', $instructor) }}" class="button button-small button-secondary">Edit</a>
                                <form action="{{ route('roomscheduling.instructors.destroy', $instructor) }}" method="POST" onsubmit="return confirm('Delete this instructor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-small button-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No instructors yet. Add the first faculty member to start planning assignments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">{{ $instructors->links() }}</div>
</div>
@endsection
