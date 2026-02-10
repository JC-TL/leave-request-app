<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    pendingRequests: Object,
    totalPending: Number,
    totalApprovedThisMonth: Number,
    totalRejectedThisMonth: Number,
    totalEmployees: Number,
    calendarEvents: Array,
});

const calendarEl = ref(null);

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function getStatusBadge(status) {
    if (status === 'pending') return { style: 'bg-yellow-100 text-yellow-800', label: 'Pending Manager' };
    if (status === 'dept_manager_approved') return { style: 'bg-blue-100 text-blue-800', label: 'Awaiting HR' };
    return { style: 'bg-gray-100 text-gray-800', label: status };
}

onMounted(async () => {
    const { Calendar } = await import('@fullcalendar/core');
    const dayGridPlugin = await import('@fullcalendar/daygrid');
    const listPlugin = await import('@fullcalendar/list');

    if (!calendarEl.value) return;

    const calendar = new Calendar(calendarEl.value, {
        plugins: [dayGridPlugin.default, listPlugin.default],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            list: 'List'
        },
        events: props.calendarEvents,
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 1.2,
        eventDisplay: 'block',
        dayMaxEvents: 2,
        fixedWeekCount: false,
    });

    calendar.render();
});
</script>

<template>
    <Head title="HR Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">HR Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Left Column -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 text-center">
                            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow">
                                <div class="flex items-center gap-2">
                                    <span class="flex-shrink-0 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 "><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    </span>
                                    <dt class="truncate text-sm font-medium text-gray-500">Pending</dt>
                                </div>
                                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ totalPending }}</dd>
                            </div>
                            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow">
                                <div class="flex items-center gap-2">
                                    <span class="flex-shrink-0 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                                    </span>
                                    <dt class="truncate text-sm font-medium text-gray-500">Approved</dt>
                                </div>
                                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ totalApprovedThisMonth }}</dd>
                            </div>
                            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow">
                                <div class="flex items-center gap-2">
                                    <span class="flex-shrink-0 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                    <dt class="truncate text-sm font-medium text-gray-500">Rejected</dt>
                                </div>
                                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ totalRejectedThisMonth }}</dd>
                            </div>
                            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow">
                                <div class="flex items-center gap-2">
                                    <span class="flex-shrink-0 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" /></svg>
                                    </span>
                                    <dt class="truncate text-sm font-medium text-gray-500">Employees</dt>
                                </div>
                                <dd class="mt-1 text-3xl font-semibold text-indigo-600">{{ totalEmployees }}</dd>
                            </div>
                        </div>

                        <!-- Pending Requests Table -->
                        <div class="bg-white p-6 shadow sm:rounded-lg">
                            
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Pending Requests</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Leave Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dates</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="request in pendingRequests.data" :key="request.id">
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">{{ request.employee.name }}</td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">{{ request.employee.department?.name ?? 'N/A' }}</td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">{{ request.leave_type }}</td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                                {{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4">
                                                <span :class="['inline-flex rounded-full px-2 text-xs font-semibold leading-5', getStatusBadge(request.status).style]">
                                                    {{ getStatusBadge(request.status).label }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm">
                                                <Link :href="route('hr.show-request', request.id)" class="text-indigo-600 hover:text-indigo-900">
                                                    View
                                                </Link>
                                            </td>
                                        </tr>
                                        <tr v-if="pendingRequests.data.length === 0">
                                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No pending requests.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Calendar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-6 overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                <h3 class="text-sm font-medium text-gray-900">Leave Calendar</h3>
                            </div>
                            <div class="calendar-container p-4">
                                <div ref="calendarEl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Calendar Container */
.calendar-container {
    font-size: 0.8rem;
}

/* Calendar Wrapper */
.calendar-container :deep(.fc) {
    font-family: inherit;
}

/* Header Toolbar */
.calendar-container :deep(.fc .fc-toolbar) {
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem !important;
}

.calendar-container :deep(.fc .fc-toolbar-title) {
    font-size: 0.95rem !important;
    font-weight: 600;
    color: #374151;
}

/* Buttons */
.calendar-container :deep(.fc .fc-button) {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 500;
    border-radius: 0.375rem !important;
    text-transform: capitalize;
}

.calendar-container :deep(.fc .fc-button-primary) {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
}

.calendar-container :deep(.fc .fc-button-primary:hover) {
    background-color: #4338ca !important;
    border-color: #4338ca !important;
}

.calendar-container :deep(.fc .fc-button-primary:disabled) {
    background-color: #6366f1 !important;
    border-color: #6366f1 !important;
    opacity: 0.7;
}

.calendar-container :deep(.fc .fc-button-primary:not(:disabled).fc-button-active) {
    background-color: #3730a3 !important;
    border-color: #3730a3 !important;
}

.calendar-container :deep(.fc .fc-button-group) {
    gap: 1px;
}

.calendar-container :deep(.fc .fc-button-group > .fc-button) {
    border-radius: 0 !important;
}

.calendar-container :deep(.fc .fc-button-group > .fc-button:first-child) {
    border-top-left-radius: 0.375rem !important;
    border-bottom-left-radius: 0.375rem !important;
}

.calendar-container :deep(.fc .fc-button-group > .fc-button:last-child) {
    border-top-right-radius: 0.375rem !important;
    border-bottom-right-radius: 0.375rem !important;
}

/* Day Grid */
.calendar-container :deep(.fc .fc-col-header-cell) {
    padding: 0.5rem 0;
    background-color: #f9fafb;
}

.calendar-container :deep(.fc .fc-col-header-cell-cushion) {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
}

.calendar-container :deep(.fc .fc-daygrid-day) {
    min-height: 2.5rem !important;
}

.calendar-container :deep(.fc .fc-daygrid-day-number) {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    color: #374151;
}

.calendar-container :deep(.fc .fc-day-today) {
    background-color: #fef3c7 !important;
}

.calendar-container :deep(.fc .fc-day-other .fc-daygrid-day-number) {
    color: #9ca3af;
}

/* Events */
.calendar-container :deep(.fc .fc-event) {
    font-size: 0.65rem;
    padding: 1px 3px;
    border-radius: 0.25rem;
    border: none;
}

.calendar-container :deep(.fc .fc-daygrid-event) {
    margin: 1px 2px;
}

/* Scrollbar for events */
.calendar-container :deep(.fc .fc-daygrid-day-events) {
    min-height: 1rem;
}

/* More link */
.calendar-container :deep(.fc .fc-daygrid-more-link) {
    font-size: 0.65rem;
    color: #4f46e5;
}

/* Remove outer borders */
.calendar-container :deep(.fc .fc-scrollgrid) {
    border: 1px solid #e5e7eb !important;
    border-radius: 0.375rem;
    overflow: hidden;
}

.calendar-container :deep(.fc .fc-scrollgrid td:last-of-type) {
    border-right: none;
}

.calendar-container :deep(.fc .fc-scrollgrid-section > td) {
    border: none;
}

.calendar-container :deep(.fc th),
.calendar-container :deep(.fc td) {
    border-color: #e5e7eb;
}
</style>
