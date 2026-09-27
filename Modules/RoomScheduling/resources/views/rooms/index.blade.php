@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Room inventory</div>
            <h1>Rooms</h1>
            <p>Monitor room availability, capacities, and room types across the campus.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.rooms.create') }}" class="button">+ Add room</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="eyebrow">Total rooms</div>
            <strong>{{ $rooms->total() }}</strong>
            <span>Across all buildings</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Active</div>
            <strong>{{ $rooms->where('is_active', true)->count() }}</strong>
            <span>Ready for scheduling</span>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Capacity</div>
            <strong>{{ $rooms->sum('capacity') }}</strong>
            <span>Seats available</span>
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
                        <th>Building / Floor</th>
                        <th>Capacity</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td><strong>{{ $room->code }}</strong></td>
                            <td><span class="entity-name">{{ $room->name }}</span></td>
                            <td>{{ $room->building }} {{ $room->floor ? '/ Fl. '.$room->floor : '—' }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td><span class="meta-badge muted">{{ ucfirst($room->type) }}</span></td>
                            <td>
                                <span class="status-pill {{ $room->is_active ? 'active' : 'inactive' }}">
                                    {{ $room->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="action-group">
                                <a href="{{ route('roomscheduling.rooms.edit', $room) }}" class="button button-small button-secondary">Edit</a>
                                <form action="{{ route('roomscheduling.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Delete this room?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-small button-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">No rooms yet. Add the first room to start planning the schedule.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">{{ $rooms->links() }}</div>
</div>
@endsection
