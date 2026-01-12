<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="/" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <span class="text-sm">{{ $user->name }}</span>
                <span class="badge badge-primary">{{ ucfirst($user->role) }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-base-content hover:text-base-content/70 transition-colors">Logout</button>
                </form>
            </div>
            @endauth
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl font-semibold">Manager Dashboard</h2>
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

        <!-- Section 1: Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-sm">Pending Approvals</h3>
                    <p class="text-3xl font-bold">{{ $pendingCount }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-sm">Approved This Month</h3>
                    <p class="text-3xl font-bold">{{ $approvedThisMonth }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-sm">Team Members</h3>
                    <p class="text-3xl font-bold">{{ $teamCount }}</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Pending Requests from My Team -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Pending Requests from My Team</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRequests as $request)
                                <tr>
                                    <td>{{ $request->employee->name }}</td>
                                    <td>{{ $request->leave_type }}</td>
                                    <td>{{ $request->start_date->format('M d, Y') }}</td>
                                    <td>{{ $request->end_date->format('M d, Y') }}</td>
                                    <td>{{ $request->number_of_days }}</td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge badge-warning whitespace-nowrap">Pending Your Approval</span></td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('manager.show-request', $request->id) }}" class="btn btn-sm btn-info btn-outline">View Details</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-base-content/60">No pending requests found.</td>
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

        <!-- Section 3: Team Members List -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Team Members</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Vacation Leave Balance</th>
                                <th>Total Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamMembers as $member)
                                @php
                                    $currentYear = now()->year;
                                    $vacationBalance = $member->getLeaveBalance('Vacation Leave', $currentYear);
                                    $available = $vacationBalance ? $vacationBalance->getAvailableBalance() : 0;
                                    $total = $vacationBalance ? $vacationBalance->balance : 0;
                                    $isLowBalance = $available < 3 && $available > 0;
                                @endphp
                                <tr class="{{ $isLowBalance ? 'bg-warning/10' : '' }}">
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ number_format($available, 1) }}</td>
                                    <td>{{ number_format($total, 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/60">No team members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>

