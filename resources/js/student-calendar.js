/**
 * Student Calendar Component
 * 
 * Provides Month, Week, and Agenda views for course calendar events.
 * Handles timezone conversion, real-time updates, and accessibility.
 */

import '../css/student.css';
import '../css/student-calendar.css';

class StudentCalendar {
    constructor(config) {
        this.config = config;
        this.currentView = this.loadViewPreference() || 'month';
        this.currentDate = new Date();
        this.events = [];
        this.filteredEvents = [];
        this.searchQuery = '';
        this.filter = 'all';
        this.hidePast = false;
        this.debounceTimer = null;
        this.abortController = null;
        this.echoChannel = null;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupBroadcastListener();
        // Set initial view state
        this.switchView(this.currentView);
    }

    loadViewPreference() {
        try {
            return localStorage.getItem('studentCalendarView') || 'month';
        } catch (e) {
            return 'month';
        }
    }

    saveViewPreference(view) {
        try {
            localStorage.setItem('studentCalendarView', view);
        } catch (e) {
            // Ignore localStorage errors
        }
    }

    setupEventListeners() {
        // View toggle buttons
        document.querySelectorAll('.calendar-view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.currentTarget.dataset.view;
                this.switchView(view);
            });
        });

        // Navigation buttons
        document.getElementById('calendar-prev')?.addEventListener('click', () => this.navigate(-1));
        document.getElementById('calendar-next')?.addEventListener('click', () => this.navigate(1));
        document.getElementById('calendar-today')?.addEventListener('click', () => this.goToToday());

        // Search
        document.getElementById('calendar-search')?.addEventListener('input', (e) => {
            this.searchQuery = e.target.value.toLowerCase();
            this.debounceFilter();
        });

        // Filters
        document.querySelectorAll('input[name="calendar-filter"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.filter = e.target.value;
                this.applyFilters();
            });
        });

        // Hide past toggle
        document.getElementById('hide-past')?.addEventListener('change', (e) => {
            this.hidePast = e.target.checked;
            this.applyFilters();
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    setupBroadcastListener() {
        if (typeof Echo !== 'undefined' && this.config.userId) {
            this.echoChannel = Echo.channel(`student.${this.config.userId}.calendar`)
                .listen('.CourseScheduleUpdated', (e) => {
                    this.announceUpdate();
                    // Invalidate cache and reload events
                    this.loadEvents(true);
                });
        } else {
            // Fallback polling every 60s
            setInterval(() => {
                this.loadEvents(true);
            }, 60000);
        }
    }

    announceUpdate() {
        const liveRegion = document.getElementById('calendar-aria-live');
        if (liveRegion) {
            liveRegion.textContent = this.config.translations.events_updated;
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 1000);
        }
    }

    switchView(view) {
        this.currentView = view;
        this.saveViewPreference(view);
        
        // Update button states
        document.querySelectorAll('.calendar-view-btn').forEach(btn => {
            const isActive = btn.dataset.view === view;
            btn.setAttribute('aria-pressed', isActive);
            btn.classList.toggle('active', isActive);
        });

        // Show/hide views
        document.querySelectorAll('.calendar-view').forEach(v => {
            v.classList.add('d-none');
        });
        document.getElementById(`calendar-${view}-view`)?.classList.remove('d-none');

        // Focus management
        const activeView = document.getElementById(`calendar-${view}-view`);
        if (activeView) {
            activeView.setAttribute('tabindex', '-1');
            activeView.focus();
        }

        this.render();
        this.loadEvents();
    }

    navigate(direction) {
        if (this.currentView === 'month') {
            this.currentDate.setMonth(this.currentDate.getMonth() + direction);
        } else if (this.currentView === 'week') {
            this.currentDate.setDate(this.currentDate.getDate() + (direction * 7));
        } else {
            // Agenda: navigate by month
            this.currentDate.setMonth(this.currentDate.getMonth() + direction);
        }
        this.render();
        this.loadEvents();
    }

    goToToday() {
        this.currentDate = new Date();
        this.render();
        this.loadEvents();
    }

    async loadEvents(force = false) {
        const { from, to } = this.getDateRange();
        
        // Cancel previous request
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        this.showLoading(true);
        this.hideEmpty();

        try {
            const params = new URLSearchParams({
                from: from.toISOString(),
                to: to.toISOString(),
            });

            const response = await fetch(`${this.config.apiUrl}?${params}`, {
                signal: this.abortController.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            const data = await response.json();
            this.events = data.events || [];
            this.applyFilters();
            this.render();
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Failed to load calendar events:', error);
                // Show user-friendly error message
                const emptyEl = document.getElementById('calendar-empty');
                if (emptyEl) {
                    emptyEl.innerHTML = `
                        <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Error loading calendar</h5>
                        <p class="text-muted">Unable to load calendar events. Please try refreshing the page.</p>
                    `;
                    emptyEl.classList.remove('d-none');
                }
            }
        } finally {
            this.showLoading(false);
        }
    }

    getDateRange() {
        const date = new Date(this.currentDate);
        let from, to;

        if (this.currentView === 'month') {
            // First day of month
            from = new Date(date.getFullYear(), date.getMonth(), 1);
            // Last day of month
            to = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            // Add buffer days
            from.setDate(from.getDate() - 7);
            to.setDate(to.getDate() + 7);
        } else if (this.currentView === 'week') {
            // Start of week (Monday)
            const day = date.getDay();
            const diff = date.getDate() - day + (day === 0 ? -6 : 1);
            from = new Date(date);
            from.setDate(diff);
            from.setHours(0, 0, 0, 0);
            // End of week (Sunday)
            to = new Date(from);
            to.setDate(to.getDate() + 6);
            to.setHours(23, 59, 59, 999);
        } else {
            // Agenda: current month with buffer
            from = new Date(date.getFullYear(), date.getMonth(), 1);
            to = new Date(date.getFullYear(), date.getMonth() + 2, 0);
        }

        return { from, to };
    }

    debounceFilter() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.applyFilters();
        }, 300);
    }

    applyFilters() {
        let filtered = [...this.events];

        // Search filter
        if (this.searchQuery) {
            filtered = filtered.filter(event => 
                event.title.toLowerCase().includes(this.searchQuery)
            );
        }

        // Type filter
        if (this.filter === 'enrolled') {
            filtered = filtered.filter(event => event.is_enrolled);
        } else if (this.filter === 'favorites') {
            // TODO: Implement favorites filter when wishlist integration is available
            filtered = filtered.filter(event => event.is_enrolled);
        }

        // Hide past
        if (this.hidePast) {
            const now = new Date();
            filtered = filtered.filter(event => {
                const eventDate = new Date(event.end_utc || event.start_utc);
                return eventDate >= now;
            });
        }

        this.filteredEvents = filtered;
        this.render();
    }

    render() {
        if (this.currentView === 'month') {
            this.renderMonth();
        } else if (this.currentView === 'week') {
            this.renderWeek();
        } else {
            this.renderAgenda();
        }

        if (this.filteredEvents.length === 0 && !this.isLoading) {
            this.showEmpty();
        }
    }

    renderMonth() {
        const date = new Date(this.currentDate);
        const year = date.getFullYear();
        const month = date.getMonth();

        // Update title
        const monthTitle = date.toLocaleDateString(this.config.locale, { 
            month: 'long', 
            year: 'numeric' 
        });
        document.getElementById('calendar-month-title').textContent = monthTitle;

        // Get first day of month and days in month
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = (firstDay.getDay() + 6) % 7; // Monday = 0

        // Build grid
        const grid = document.getElementById('calendar-month-grid');
        if (!grid) return;

        grid.innerHTML = `
            <div class="calendar-weekdays d-flex">
                <div class="calendar-weekday text-center fw-bold p-2">Mon</div>
                <div class="calendar-weekday text-center fw-bold p-2">Tue</div>
                <div class="calendar-weekday text-center fw-bold p-2">Wed</div>
                <div class="calendar-weekday text-center fw-bold p-2">Thu</div>
                <div class="calendar-weekday text-center fw-bold p-2">Fri</div>
                <div class="calendar-weekday text-center fw-bold p-2">Sat</div>
                <div class="calendar-weekday text-center fw-bold p-2">Sun</div>
            </div>
            <div class="calendar-days d-flex flex-wrap" role="grid"></div>
        `;

        const daysContainer = grid.querySelector('.calendar-days');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Empty cells for days before month start
        for (let i = 0; i < startDay; i++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-day empty';
            daysContainer.appendChild(cell);
        }

        // Days of month
        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-day';
            cell.setAttribute('role', 'gridcell');
            cell.setAttribute('tabindex', '0');
            
            const cellDate = new Date(year, month, day);
            cell.dataset.date = cellDate.toISOString().split('T')[0];
            
            if (cellDate.getTime() === today.getTime()) {
                cell.classList.add('today');
            }

            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = day;
            cell.appendChild(dayNumber);

            // Events for this day
            const dayEvents = this.getEventsForDate(cellDate);
            if (dayEvents.length > 0) {
                const eventsContainer = document.createElement('div');
                eventsContainer.className = 'calendar-day-events';
                
                dayEvents.slice(0, 3).forEach(event => {
                    const eventDot = document.createElement('div');
                    eventDot.className = 'calendar-event-dot';
                    eventDot.setAttribute('title', event.title);
                    eventDot.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.showEventDetails(event);
                    });
                    eventsContainer.appendChild(eventDot);
                });

                if (dayEvents.length > 3) {
                    const more = document.createElement('div');
                    more.className = 'calendar-event-more';
                    more.textContent = `+${dayEvents.length - 3}`;
                    eventsContainer.appendChild(more);
                }

                cell.appendChild(eventsContainer);
            }

            cell.addEventListener('click', () => this.showEventDetailsForDate(cellDate));
            cell.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.showEventDetailsForDate(cellDate);
                }
            });

            daysContainer.appendChild(cell);
        }
    }

    renderWeek() {
        const date = new Date(this.currentDate);
        const day = date.getDay();
        const diff = date.getDate() - day + (day === 0 ? -6 : 1);
        const monday = new Date(date);
        monday.setDate(diff);
        monday.setHours(0, 0, 0, 0);

        const weekTitle = `Week of ${monday.toLocaleDateString(this.config.locale, { 
            month: 'short', 
            day: 'numeric',
            year: 'numeric'
        })}`;
        document.getElementById('calendar-week-title').textContent = weekTitle;

        const grid = document.getElementById('calendar-week-grid');
        if (!grid) return;

        grid.innerHTML = '<div class="calendar-week-time-columns"></div>';
        const columns = grid.querySelector('.calendar-week-time-columns');

        // Create columns for each day
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(monday);
            dayDate.setDate(monday.getDate() + i);

            const column = document.createElement('div');
            column.className = 'calendar-week-column';
            column.innerHTML = `
                <div class="calendar-week-day-header">
                    <div class="fw-bold">${dayDate.toLocaleDateString(this.config.locale, { weekday: 'short' })}</div>
                    <div class="text-muted small">${dayDate.getDate()}</div>
                </div>
                <div class="calendar-week-day-events" data-date="${dayDate.toISOString().split('T')[0]}"></div>
            `;

            const eventsContainer = column.querySelector('.calendar-week-day-events');
            const dayEvents = this.getEventsForDate(dayDate);
            dayEvents.forEach(event => {
                const eventEl = this.createEventElement(event);
                eventsContainer.appendChild(eventEl);
            });

            columns.appendChild(column);
        }
    }

    renderAgenda() {
        const date = new Date(this.currentDate);
        const monthTitle = date.toLocaleDateString(this.config.locale, { 
            month: 'long', 
            year: 'numeric' 
        });
        document.getElementById('calendar-agenda-title').textContent = monthTitle;

        const list = document.getElementById('calendar-agenda-list');
        if (!list) return;

        // Group events by date
        const grouped = this.groupEventsByDate(this.filteredEvents);

        if (grouped.length === 0) {
            list.innerHTML = '<p class="text-muted text-center py-4">' + (this.config.translations?.no_events_period || 'No events in this period') + '</p>';
            return;
        }

        list.innerHTML = '';
        grouped.forEach(({ date, events }) => {
            const dateHeader = document.createElement('div');
            dateHeader.className = 'calendar-agenda-date-header fw-bold mb-2 mt-4';
            dateHeader.textContent = date.toLocaleDateString(this.config.locale, {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            list.appendChild(dateHeader);

            events.forEach(event => {
                const eventEl = this.createAgendaEventElement(event);
                list.appendChild(eventEl);
            });
        });
    }

    createEventElement(event) {
        const el = document.createElement('div');
        el.className = 'calendar-event-item';
        el.setAttribute('role', 'button');
        el.setAttribute('tabindex', '0');
        el.setAttribute('aria-label', `${event.title} - ${this.formatEventTime(event)}`);

        const start = new Date(event.start_utc);
        const end = event.end_utc ? new Date(event.end_utc) : null;

        el.innerHTML = `
            <div class="calendar-event-title fw-semibold">${this.escapeHtml(event.title)}</div>
            <div class="calendar-event-time text-muted small">${this.formatEventTime(event)}</div>
        `;

        el.addEventListener('click', () => this.showEventDetails(event));
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.showEventDetails(event);
            }
        });

        return el;
    }

    createAgendaEventElement(event) {
        const el = document.createElement('div');
        el.className = 'card mb-2 calendar-agenda-event';
        el.setAttribute('role', 'button');
        el.setAttribute('tabindex', '0');

        const start = new Date(event.start_utc);
        const end = event.end_utc ? new Date(event.end_utc) : null;

        el.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">${this.escapeHtml(event.title)}</h6>
                        <p class="card-text text-muted small mb-0">
                            ${this.formatEventTime(event)}
                        </p>
                    </div>
                    ${event.is_enrolled ? '<span class="badge bg-success">Enrolled</span>' : ''}
                </div>
            </div>
        `;

        el.addEventListener('click', () => this.showEventDetails(event));
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.showEventDetails(event);
            }
        });

        return el;
    }

    getEventsForDate(date) {
        const dateStr = date.toISOString().split('T')[0];
        return this.filteredEvents.filter(event => {
            const eventStart = new Date(event.start_utc).toISOString().split('T')[0];
            const eventEnd = event.end_utc ? new Date(event.end_utc).toISOString().split('T')[0] : eventStart;
            return dateStr >= eventStart && dateStr <= eventEnd;
        });
    }

    groupEventsByDate(events) {
        const grouped = new Map();
        events.forEach(event => {
            const date = new Date(event.start_utc);
            date.setHours(0, 0, 0, 0);
            const key = date.toISOString();
            if (!grouped.has(key)) {
                grouped.set(key, { date, events: [] });
            }
            grouped.get(key).events.push(event);
        });
        return Array.from(grouped.values()).sort((a, b) => a.date - b.date);
    }

    formatEventTime(event) {
        const start = new Date(event.start_utc);
        const end = event.end_utc ? new Date(event.end_utc) : null;

        const startStr = start.toLocaleString(this.config.locale, {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZoneName: 'short'
        });

        if (event.type === 'window' && end) {
            const endStr = end.toLocaleString(this.config.locale, {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZoneName: 'short'
            });
            return `${startStr} - ${endStr}`;
        } else if (event.type === 'start') {
            return `${this.config.translations.starts_at} ${startStr}`;
        } else {
            return `${this.config.translations.ends_at} ${startStr}`;
        }
    }

    showEventDetails(event) {
        const modalEl = document.getElementById('event-details-modal');
        if (!modalEl) return;
        
        // Get Bootstrap Modal - check if available globally or via window
        const BootstrapModal = window.bootstrap?.Modal || bootstrap?.Modal;
        if (!BootstrapModal) {
            console.error('Bootstrap Modal not available');
            return;
        }
        
        const modal = new BootstrapModal(modalEl);
        const content = document.getElementById('event-details-content');
        const goToCourseBtn = document.getElementById('event-go-to-course');

        const start = new Date(event.start_utc);
        const end = event.end_utc ? new Date(event.end_utc) : null;

        let html = `
            <h6 class="fw-bold mb-3">${this.escapeHtml(event.title)}</h6>
            <div class="mb-3">
                <p class="mb-1"><strong>${this.config.translations.starts_at}:</strong></p>
                <p class="text-muted">${this.formatEventTime(event)}</p>
            </div>
        `;

        if (event.type === 'window' && end) {
            html += `
                <div class="mb-3">
                    <p class="mb-1"><strong>${this.config.translations.ends_at}:</strong></p>
                    <p class="text-muted">${end.toLocaleString(this.config.locale, {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZoneName: 'short'
                    })}</p>
                </div>
            `;
        }

        const relativeTime = this.getRelativeTime(start);
        if (relativeTime) {
            html += `<p class="text-muted small">${this.config.translations.in.replace(':relative', relativeTime)}</p>`;
        }

        content.innerHTML = html;

        if (event.course_id_for_url || event.course_id) {
            const courseId = event.course_id_for_url || event.course_id;
            const courseUrl = this.config.courseUrlTemplate?.replace(':id', courseId) || `/courses/${courseId}`;
            goToCourseBtn.href = courseUrl;
            goToCourseBtn.style.display = 'inline-block';
        } else {
            goToCourseBtn.style.display = 'none';
        }

        modal.show();

        // Focus management
        goToCourseBtn.focus();
    }

    showEventDetailsForDate(date) {
        const events = this.getEventsForDate(date);
        if (events.length === 1) {
            this.showEventDetails(events[0]);
        } else if (events.length > 1) {
            // Show list of events for this date
            // For simplicity, show first event
            this.showEventDetails(events[0]);
        }
    }

    getRelativeTime(date) {
        const now = new Date();
        const diff = date - now;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) {
            return `${days} ${days === 1 ? 'day' : 'days'}`;
        } else if (hours > 0) {
            return `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
        } else if (minutes > 0) {
            return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'}`;
        }
        return null;
    }

    showLoading(show) {
        this.isLoading = show;
        const loading = document.getElementById('calendar-loading');
        if (loading) {
            loading.classList.toggle('d-none', !show);
        }
    }

    showEmpty() {
        const empty = document.getElementById('calendar-empty');
        if (empty) {
            empty.classList.remove('d-none');
        }
    }

    hideEmpty() {
        const empty = document.getElementById('calendar-empty');
        if (empty) {
            empty.classList.add('d-none');
        }
    }

    handleKeyboard(e) {
        // Arrow key navigation for calendar grid
        if (e.target.classList.contains('calendar-day')) {
            const current = e.target;
            let next;

            switch (e.key) {
                case 'ArrowRight':
                    next = current.nextElementSibling;
                    if (next && next.classList.contains('calendar-day')) {
                        next.focus();
                        e.preventDefault();
                    }
                    break;
                case 'ArrowLeft':
                    next = current.previousElementSibling;
                    if (next && next.classList.contains('calendar-day')) {
                        next.focus();
                        e.preventDefault();
                    }
                    break;
                case 'ArrowDown':
                    const row = current.parentElement;
                    const index = Array.from(row.children).indexOf(current);
                    const nextRow = row.parentElement.querySelectorAll('.calendar-days > div')[index + 7];
                    if (nextRow) {
                        nextRow.focus();
                        e.preventDefault();
                    }
                    break;
                case 'ArrowUp':
                    const row2 = current.parentElement;
                    const index2 = Array.from(row2.children).indexOf(current);
                    const prevRow = row2.parentElement.querySelectorAll('.calendar-days > div')[index2 - 7];
                    if (prevRow) {
                        prevRow.focus();
                        e.preventDefault();
                    }
                    break;
            }
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.CalendarConfig && document.getElementById('student-calendar-app')) {
        window.studentCalendar = new StudentCalendar(window.CalendarConfig);
    }
});

export default StudentCalendar;

