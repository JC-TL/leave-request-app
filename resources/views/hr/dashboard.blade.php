<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('hr.dashboard') }}" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                
                <span class="text-sm">{{ auth()->user()->name }}</span>
                <span class="badge badge-primary">{{ ucfirst(auth()->user()->role) }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-base-content hover:text-base-content/70 transition-colors">Logout</button>
                </form>
            </div>
            @endauth
            <details class="dropdown">
                <summary class="btn m-1">open or close</summary>
                <ul class="menu dropdown-content bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                    <li><<a href="{{ route('hr.policies') }}" class="btn btn-sm btn-outline">Policies</a></li>
                    <li><a>Item 2</a></li>
                </ul>
            </details>
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl font-semibold">HR Admin Dashboard</h2>
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
                        <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 1024 1024"
    fill="currentColor"
    class="w-20 h-20 text-blue-900"
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-20">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>

                        <h3 class="card-title text-lg">Approved This Month</h3>
                        <p class="text-4xl font-bold">{{ $totalApprovedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="card-title text-sm">Rejected This Month</h3>
                        <p class="text-3xl font-bold">{{ $totalRejectedThisMonth }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="card-title text-sm">Total Employees</h3>
                        <p class="text-3xl font-bold">{{ $totalEmployees }}</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pending Requests Awaiting HR Approval -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-4">Pending Requests Awaiting HR Approval</h2>
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
                                            <a href="{{ route('hr.show-request', $request->id) }}" class="btn btn-sm btn-secondary btn-outline">View Details</a>
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
                        <h2 class="card-title text-xl mb-4">Calendar</h2>
                        <div class="flex items-center justify-center min-h-[400px] bg-base-200 rounded-lg">
                            <p class="text-base-content/60 text-center">Event Calendar here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

