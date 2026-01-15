<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="/" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <details class="dropdown dropdown-end">
                    <summary class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </summary>
                    <ul class="menu dropdown-content bg-base-100 rounded-box z-[1] w-56 p-2 shadow-lg border border-base-300 mt-2">
                        <li class="menu-title">
                            <span>{{ $user->name }}</span>
                            <span class="badge badge-primary badge-sm ml-2">{{ ucfirst($user->role) }}</span>
                        </li>
                        <li class="divider my-1"></li>
                        <li>
                            <a href="{{ route('manager.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
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
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Manager Dashboard</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4">
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

        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Pending Requests from My Team</h2>
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
                                            <a href="{{ route('manager.show-request', $request->id) }}" class="btn btn-sm border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">View Details</a>
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

        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Team Members</h2>
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
    </div>
</x-layout>

