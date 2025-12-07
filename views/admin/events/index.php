<?php
// Extract data
$events = $events ?? [];
$stats = $stats ?? ['total' => 0, 'upcoming' => 0, 'past' => 0];
?>

<div class="row">
    <!-- Event Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Events
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Upcoming Events
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['upcoming']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    This Month
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $thisMonth = array_filter($events, function($event) {
                                        return date('Y-m', strtotime($event['event_date'])) === date('Y-m');
                                    });
                                    echo count($thisMonth);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Past Events
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['past']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-history fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Events</h5>
            </div>
            <div class="card-body">
                <?php if (empty($events)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No events found</h5>
                        <p class="text-muted">Start by creating your first event.</p>
                        <a href="/admin/events/create" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create First Event
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($event['image_path']): ?>
                                                    <img src="<?php echo htmlspecialchars($event['image_path']); ?>"
                                                         alt="Event" class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                                    <?php if ($event['description']): ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars(substr($event['description'], 0, 50)) . (strlen($event['description']) > 50 ? '...' : ''); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><?php echo date('d M Y', strtotime($event['event_date'])); ?></div>
                                            <small class="text-muted"><?php echo date('l', strtotime($event['event_date'])); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($event['location'] ?: 'TBA'); ?></td>
                                        <td>
                                            <?php
                                            $today = date('Y-m-d');
                                            if (strtotime($event['event_date']) < strtotime($today)) {
                                                echo '<span class="badge bg-secondary">Past</span>';
                                            } elseif (strtotime($event['event_date']) == strtotime($today)) {
                                                echo '<span class="badge bg-success">Today</span>';
                                            } else {
                                                echo '<span class="badge bg-info">Upcoming</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($event['created_by_name'] ?: 'Unknown'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/events/<?php echo $event['id']; ?>"
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/events/<?php echo $event['id']; ?>/edit"
                                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                        onclick="deleteEvent(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEvent(eventId, eventTitle) {
    if (confirm(`Are you sure you want to delete "${eventTitle}"? This action cannot be undone.`)) {
        fetch(`/admin/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the event.');
        });
    }
}
</script>