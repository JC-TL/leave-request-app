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

    <div class="max-w-6xl mx-auto px-4">
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
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 1024 1024"
                            fill="currentColor"
                            class="w-20 h-20 text-blue-900 mb-2"
                        >
                            <path d="M511.9 183c-181.8 0-329.1 147.4-329.1 329.1s147.4 329.1 329.1 329.1c181.8 0 329.1-147.4 329.1-329.1S693.6 183 511.9 183z m0 585.2c-141.2 0-256-114.8-256-256s114.8-256 256-256 256 114.8 256 256-114.9 256-256 256z"></path>
                            <path d="M548.6 365.7h-73.2v161.4l120.5 120.5 51.7-51.7-99-99z"></path>
                        </svg>
                        <h3 class="card-title text-lg">Pending Requests</h3>
                        <p class="text-4xl font-bold">{{ $totalPending }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-green-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="card-title text-lg">Approved This Month</h3>
                        <p class="text-4xl font-bold">{{ $totalApprovedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-20 h-20 text-red-500 mb-2"
                        >
                            <path d="M18,18c-0.55,0-1,0.45-1,1v1H6V4h6v5c0,0.55,0.45,1,1,1h4v1c0,0.55,0.45,1,1,1s1-0.45,1-1V9c0-0.13-0.03-0.25-0.07-0.37c-0.02-0.04-0.04-0.08-0.07-0.11c-0.03-0.05-0.05-0.11-0.09-0.16l-5-6c-0.01-0.01-0.02-0.02-0.03-0.03c-0.07-0.07-0.15-0.13-0.23-0.18c-0.03-0.02-0.06-0.05-0.1-0.06C13.28,2.03,13.15,2,13,2H5C4.45,2,4,2.45,4,3v18c0,0.55,0.45,1,1,1h13c0.55,0,1-0.45,1-1v-2C19,18.45,18.55,18,18,18z M14,5.76L15.86,8H14V5.76z"></path>
                            <path d="M8,10h2c0.55,0,1-0.45,1-1s-0.45-1-1-1H8C7.45,8,7,8.45,7,9S7.45,10,8,10z"></path>
                            <path d="M13,11H8c-0.55,0-1,0.45-1,1s0.45,1,1,1h5c0.55,0,1-0.45,1-1S13.55,11,13,11z"></path>
                            <path d="M13,14H8c-0.55,0-1,0.45-1,1s0.45,1,1,1h5c0.55,0,1-0.45,1-1S13.55,14,13,14z"></path>
                            <path d="M13,17H8c-0.55,0-1,0.45-1,1s0.45,1,1,1h5c0.55,0,1-0.45,1-1S13.55,17,13,17z"></path>
                            <path d="M20.71,12.29c-0.39-0.39-1.02-0.39-1.41,0L18,13.59l-1.29-1.29c-0.39-0.39-1.02-0.39-1.41,0s-0.39,1.02,0,1.41L16.59,15l-1.29,1.29c-0.39,0.39-0.39,1.02,0,1.41s1.02,0.39,1.41,0L18,16.41l1.29,1.29C19.49,17.9,19.74,18,20,18s0.51-0.1,0.71-0.29c0.39-0.39,0.39-1.02,0-1.41L19.41,15l1.29-1.29C21.1,13.32,21.1,12.68,20.71,12.29z"></path>
                        </svg>
                        <h3 class="card-title text-lg">Rejected This Month</h3>
                        <p class="text-4xl font-bold">{{ $totalRejectedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body items-center text-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                            fill="currentColor"
                            class="w-20 h-20 text-purple-500 mb-2"
                        >
                            <path d="M375.071,86.028c-11.366,0-22.143,2.561-31.796,7.122c3.686,4.748,6.998,9.802,9.882,15.121c2.828,5.216,5.244,10.688,7.214,16.364c3.928,11.321,6.069,23.469,6.069,36.109c0,12.639-2.141,24.788-6.069,36.108c-1.969,5.678-4.386,11.147-7.214,16.364c-2.884,5.319-6.195,10.372-9.882,15.121c9.653,4.56,20.43,7.123,31.796,7.123c41.199,0.002,74.716-33.516,74.716-74.714C449.787,119.545,416.27,86.028,375.071,86.028z"></path>
                            <path d="M375.071,271.182c-4.42,0-8.827,0.218-13.206,0.641c6.82,5.311,13.237,11.115,19.187,17.369c6.005,6.311,11.53,13.079,16.534,20.237c16.349,23.386,27.066,50.987,30.146,80.823c0.607,5.873,0.92,11.83,0.92,17.86c0,6.261-1.09,12.27-3.072,17.86h68.56c9.864,0,17.86-7.998,17.86-17.86C512.001,332.608,450.574,271.182,375.071,271.182z"></path>
                            <path d="M151.632,196.855c-3.928-11.32-6.069-23.469-6.069-36.108c0-12.64,2.141-24.788,6.069-36.109c1.971-5.68,4.386-11.15,7.214-16.366c2.884-5.319,6.195-10.372,9.882-15.121c-9.653-4.56-20.43-7.122-31.796-7.122c-41.199,0-74.716,33.517-74.716,74.716c0,41.198,33.517,74.716,74.716,74.716c11.366,0,22.143-2.562,31.796-7.123c-3.686-4.749-6.998-9.802-9.882-15.121C156.018,208.002,153.602,202.532,151.632,196.855z"></path>
                            <path d="M136.93,271.182C61.427,271.182,0,332.608,0,408.112c0,9.863,7.997,17.86,17.86,17.86h68.56c-1.981-5.59-3.071-11.6-3.071-17.86c0-6.031,0.313-11.988,0.919-17.86c3.08-29.836,13.797-57.437,30.146-80.823c5.005-7.158,10.529-13.926,16.534-20.237c5.95-6.254,12.367-12.058,19.187-17.369C145.757,271.4,141.35,271.182,136.93,271.182z"></path>
                            <path d="M325.393,133.094c-2.509-6.271-5.831-12.13-9.857-17.433c-13.657-17.988-35.257-29.633-59.535-29.633s-45.878,11.645-59.535,29.635c-4.026,5.303-7.348,11.162-9.857,17.433c-3.421,8.559-5.325,17.883-5.325,27.649c0,9.765,1.904,19.089,5.325,27.648c2.509,6.271,5.831,12.13,9.857,17.433c13.657,17.988,35.257,29.634,59.535,29.634s45.878-11.646,59.535-29.636c4.026-5.303,7.348-11.162,9.857-17.433c3.421-8.559,5.325-17.882,5.325-27.648S328.814,141.653,325.393,133.094z"></path>
                            <path d="M391.768,390.252c-4.11-31.402-18.901-59.488-40.594-80.489c-5.137-4.971-10.656-9.547-16.515-13.672c-6.044-4.256-12.444-8.04-19.149-11.288c-12.892-6.246-26.905-10.528-41.647-12.457v111.953c0,9.863-7.997,17.86-17.86,17.86c-9.864,0-17.86-7.998-17.86-17.86V272.346c-14.743,1.929-28.755,6.211-41.648,12.457c-6.705,3.249-13.105,7.032-19.149,11.288c-5.859,4.126-11.38,8.702-16.515,13.672c-21.695,21-36.485,49.087-40.594,80.489c-0.764,5.846-1.163,11.807-1.163,17.86c0,9.863,7.997,17.86,17.86,17.86h238.14c9.864,0,17.86-7.998,17.86-17.86C392.933,402.059,392.534,396.098,391.768,390.252z"></path>
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
                                                <span class="badge badge-info">Manager Approved - Waiting HR</span>
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

