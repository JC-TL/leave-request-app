<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('hr.dashboard') }}" class="text-lg font-semibold">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20">
                </a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <details class="dropdown dropdown-end">
                    <summary class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </summary>
                    <ul class="menu dropdown-content bg-base-100 rounded-box z-[1] w-56 p-2 shadow-lg border border-base-300 mt-2">
                        <li class="menu-title">
                            <span>{{ auth()->user()->name }}</span>
                            <span class="badge badge-primary badge-sm ml-2">{{ ucfirst(auth()->user()->role) }}</span>
                        </li>
                        <li class="divider my-1"></li>
                        <li>
                            <a href="{{ route('hr.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.policies') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125V8.25zm6.75 0v8.25m0-8.25h3.375c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125H16.5V8.25z" />
                                </svg>
                                Policies
                            </a>
                        </li>
                        <li class="divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </details>
            </div>
            @endauth
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">HR Admin Dashboard</h2>
    </x-slot>

    <div class="w-full max-w-[1920px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Content -->
            <div class="lg:col-span-2 space-y-6">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
        

            <!-- Section 1: Summary Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-amber-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="card-title text-lg">Pending Requests</h3>
                        <p class="text-4xl font-bold">{{ $totalPending }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-green-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        <h3 class="card-title text-lg">Approved This Month</h3>
                        <p class="text-4xl font-bold">{{ $totalApprovedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-red-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="card-title text-lg">Rejected This Month</h3>
                        <p class="text-4xl font-bold">{{ $totalRejectedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-purple-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <h3 class="card-title text-lg">Total Employees</h3>
                        <p class="text-4xl font-bold">{{ $totalEmployees }}</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pending Requests Awaiting HR Approval -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Pending Requests Awaiting HR Approval</h2>
                    <div class="overflow-x-auto w-full">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days</th>
                                    <th>Manager Approval Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingRequests as $request)
                                    <tr>
                                        <td>{{ $request->employee->name }}</td>
                                        <td>{{ $request->employee->department->name ?? 'N/A' }}</td>
                                        <td>{{ $request->leave_type }}</td>
                                        <td>{{ $request->start_date->format('M d, Y') }}</td>
                                        <td>{{ $request->end_date->format('M d, Y') }}</td>
                                        <td>{{ $request->number_of_days }}</td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <span class="badge badge-warning">Pending Manager</span>
                                            @elseif($request->status === 'dept_manager_approved')
                                                <span class="badge badge-info">Waiting HR</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('hr.show-request', $request->id) }}" class="btn btn-sm border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">View Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-base-content/60">No pending requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($pendingRequests->hasPages())
                        <div class="mt-4">
                            {{ $pendingRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

            <!-- Right Column: Calendar -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-sm border border-base-300 sticky top-6">
                    <div class="card-body">
                        <h2 class="card-title text-xl text-[#1C96E1] mb-4">Leave Calendar</h2>
                        <div id="calendar" class="min-h-[400px]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Style calendar events as small circles */
        .fc-event {
            border-radius: 50% !important;
            width: 10px !important;
            height: 10px !important;
            border: none !important;
            padding: 0 !important;
            margin: 2px !important;
            display: inline-block !important;
            font-size: 0 !important;
            line-height: 0 !important;
            min-height: 10px !important;
            overflow: hidden !important;
        }
        
        .fc-event-title {
            display: none !important;
            visibility: hidden !important;
        }
        
        .fc-event-title-container {
            display: none !important;
        }
        
        .fc-daygrid-event {
            margin: 2px !important;
            padding: 0 !important;
        }
        
        .fc-daygrid-day-events {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            min-height: 20px;
            gap: 2px;
        }
        
        .fc-daygrid-day-frame {
            display: flex;
            flex-direction: column;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: @json($calendarEvents),
                    eventDisplay: 'block',
                    height: 'auto',
                    dayMaxEvents: false, // Show all events
                    eventDidMount: function(info) {
                        // Ensure events appear as small circles
                        info.el.style.borderRadius = '50%';
                        info.el.style.width = '10px';
                        info.el.style.height = '10px';
                        info.el.style.border = 'none';
                        info.el.style.padding = '0';
                        info.el.style.margin = '2px';
                        info.el.style.display = 'inline-block';
                        info.el.style.fontSize = '0';
                        info.el.style.lineHeight = '0';
                        info.el.style.minHeight = '10px';
                        
                        // Set tooltip
                        info.el.title = info.event.extendedProps.employee + ' - ' + 
                                       info.event.extendedProps.leave_type + 
                                       ' (' + info.event.extendedProps.days + ' days)';
                        
                        // Hide text content
                        var titleEl = info.el.querySelector('.fc-event-title');
                        if (titleEl) {
                            titleEl.style.display = 'none';
                        }
                    },
                    eventClick: function(info) {
                        // Show event details on click
                        alert('Employee: ' + info.event.extendedProps.employee + '\n' +
                              'Department: ' + info.event.extendedProps.department + '\n' +
                              'Leave Type: ' + info.event.extendedProps.leave_type + '\n' +
                              'Days: ' + info.event.extendedProps.days);
                        info.jsEvent.preventDefault();
                    }
                });
                calendar.render();
            }
        });
    </script>
</x-layout>

