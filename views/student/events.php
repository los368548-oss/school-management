<?php
// Extract data
$upcoming_events = $upcoming_events ?? [];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">School Events & Announcements</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($upcoming_events)): ?>
                    <div class="row">
                        <?php foreach ($upcoming_events as $event): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-left-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                            <span class="badge bg-primary">
                                                <?php echo date('M d', strtotime($event['event_date'])); ?>
                                            </span>
                                        </div>

                                        <p class="card-text small text-muted mb-2">
                                            <?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 150)); ?>
                                            <?php if (strlen($event['description'] ?? '') > 150): ?>...<?php endif; ?>
                                        </p>

                                        <div class="row small">
                                            <div class="col-6">
                                                <strong>Date:</strong><br>
                                                <?php echo date('l, F j, Y', strtotime($event['event_date'])); ?>
                                            </div>
                                            <div class="col-6">
                                                <strong>Time:</strong><br>
                                                <?php echo date('h:i A', strtotime($event['event_date'])); ?>
                                            </div>
                                        </div>

                                        <?php if ($event['location']): ?>
                                            <div class="mt-2 small">
                                                <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-3">
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewEventDetails(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </button>
                                            <button class="btn btn-outline-success btn-sm" onclick="addToCalendar(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-calendar-plus me-1"></i>Add to Calendar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Event Calendar View -->
                    <div class="mt-4">
                        <h6>Event Calendar</h6>
                        <div class="calendar border rounded p-3">
                            <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Upcoming Events</h6>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="previousMonth()">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="nextMonth()">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="timeline">
                                <?php
                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                $monthEvents = array_filter($upcoming_events, function($event) use ($currentMonth, $currentYear) {
                                    $eventMonth = date('m', strtotime($event['event_date']));
                                    $eventYear = date('Y', strtotime($event['event_date']));
                                    return $eventMonth == $currentMonth && $eventYear == $currentYear;
                                });
                                ?>

                                <?php if (!empty($monthEvents)): ?>
                                    <?php foreach ($monthEvents as $event): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                                <div class="timeline-text">
                                                    <?php echo htmlspecialchars($event['description'] ?? ''); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('l, F j, Y \a\t h:i A', strtotime($event['event_date'])); ?>
                                                    <?php if ($event['location']): ?>
                                                        | <?php echo htmlspecialchars($event['location']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small">No events scheduled for this month</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                        <h4>No Upcoming Events</h4>
                        <p class="text-muted">There are no upcoming events scheduled at the moment.</p>
                        <p class="text-muted small">Check back later for updates on school events and activities.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addToCalendarFromModal()">
                    <i class="fas fa-calendar-plus me-1"></i>Add to Calendar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Categories -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Event Categories</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 border-primary">
                            <div class="card-body">
                                <i class="fas fa-graduation-cap fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Academic</h6>
                                <p class="card-text small text-muted">Exams, results, academic events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 border-success">
                            <div class="card-body">
                                <i class="fas fa-trophy fa-2x text-success mb-2"></i>
                                <h6 class="card-title">Sports</h6>
                                <p class="card-text small text-muted">Sports day, competitions, matches</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 border-info">
                            <div class="card-body">
                                <i class="fas fa-palette fa-2x text-info mb-2"></i>
                                <h6 class="card-title">Cultural</h6>
                                <p class="card-text small text-muted">Festivals, cultural programs, arts</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 border-warning">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                                <h6 class="card-title">General</h6>
                                <p class="card-text small text-muted">PTA meetings, workshops, seminars</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
    background: #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 16px;
    font-weight: 600;
    color: #495057;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 14px;
    color: #6c757d;
}

.calendar {
    background: #f8f9fa;
}
</style>

<script>
let currentEventId = null;

function viewEventDetails(eventId) {
    currentEventId = eventId;

    // Show modal with loading
    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    modal.show();

    // In a real implementation, you would fetch event details via AJAX
    // For now, show a placeholder
    document.getElementById('eventModalBody').innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Event details feature will be available with full implementation.
        </div>
        <p>Detailed event information including agenda, requirements, and registration details will be displayed here.</p>
    `;
}

function addToCalendar(eventId) {
    // In a real implementation, this would integrate with calendar APIs
    alert('Add to calendar feature will be implemented soon. You can manually add this event to your personal calendar.');
}

function addToCalendarFromModal() {
    if (currentEventId) {
        addToCalendar(currentEventId);
    }
}

function previousMonth() {
    // In a real implementation, this would change the month and reload events
    alert('Month navigation will be implemented with full calendar integration.');
}

function nextMonth() {
    // In a real implementation, this would change the month and reload events
    alert('Month navigation will be implemented with full calendar integration.');
}
</script>