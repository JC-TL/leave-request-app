<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('hr.dashboard') }}" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <a href="{{ route('hr.policies') }}" class="btn btn-sm btn-outline">Policies</a>
                <span class="text-sm">{{ auth()->user()->name }}</span>
                <span class="badge badge-primary">{{ ucfirst(auth()->user()->role) }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-base-content hover:text-base-content/70 transition-colors">Logout</button>
                </form>
            </div>
            @endauth
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl font-semibold">HR Admin Dashboard</h2>
    </x-slot>

    <div class="space-y-6">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-sm">Pending Requests</h3>
                    <p class="text-3xl font-bold">{{ $totalPending }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-sm">Approved This Month</h3>
                    <p class="text-3xl font-bold">{{ $totalApprovedThisMonth }}</p>
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
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
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
                                        <a href="{{ route('hr.show-request', $request->id) }}" class="btn btn-sm btn-info btn-outline">View Details</a>
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
</x-layout>

