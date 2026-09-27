@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Academic sections</div>
            <h1>Sections</h1>
            <p>Keep track of every subject section, cohort size, and academic term.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.sections.create') }}" class="button">+ Add section</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="eyebrow">Total sections</div>
            <strong>{{ $sections->total() }}</strong>
            <span>Current cohort list</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Avg. size</div>
            <strong>{{ $sections->count() ? round($sections->sum('max_students') / $sections->count(), 0) : 0 }}</strong>
            <span>Students per section</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Latest term</div>
            <strong>{{ $sections->first()?->school_year ?? '—' }}</strong>
            <span>{{ $sections->first()?->semester ?? 'No data' }}</span>
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
                        <th>Section</th>
                        <th>Subject</th>
                        <th>School year</th>
                        <th>Semester</th>
                        <th>Max students</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td><strong>{{ $section->section_code }}</strong></td>
                            <td>
                                <span class="entity-name">{{ $section->subject->code ?? '—' }}</span>
                                <span class="table-subtext">{{ $section->subject->name ?? 'Unassigned subject' }}</span>
                            </td>
                            <td>{{ $section->school_year }}</td>
                            <td><span class="meta-badge muted">{{ $section->semester }}</span></td>
                            <td>{{ $section->max_students }}</td>
                            <td class="action-group">
                                <a href="{{ route('roomscheduling.sections.edit', $section) }}" class="button button-small button-secondary">Edit</a>
                                <form action="{{ route('roomscheduling.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Delete this section?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-small button-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No sections yet. Create the first section for your curriculum.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">{{ $sections->links() }}</div>
</div>
@endsection
