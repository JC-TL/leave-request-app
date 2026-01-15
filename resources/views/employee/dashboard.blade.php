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
                            <a href="{{ route('employee.dashboard') }}">
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
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Employee Dashboard</h2>
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

        <!-- Section 1: Leave Balance Display -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Leave Balance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($balances as $balance)
                        <x-balance-card :balance="$balance" />
                    @empty
                        <p class="col-span-full text-center text-base-content/60">No leave balances found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Section 2: Submit Leave Request Form -->
        @php
            $hasBalance = $balances->where('balance', '>', 0)->count() > 0;
        @endphp

        @if($hasBalance)
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Submit Leave Request</h2>
                    <form action="{{ route('employee.store-request') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Leave Type</span>
                            </label>
                            <select name="leave_type" class="select select-bordered w-full @error('leave_type') select-error @enderror" required>
                                <option value="">Select Leave Type</option>
                                @foreach($balances as $balance)
                                    @if($balance->getAvailableBalance() > 0)
                                        <option value="{{ $balance->leave_type }}" {{ old('leave_type') == $balance->leave_type ? 'selected' : '' }}>
                                            {{ $balance->leave_type }} ({{ number_format($balance->getAvailableBalance(), 1) }} days available)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('leave_type')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Start Date</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="start_date" 
                                    value="{{ old('start_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="input input-bordered w-full @error('start_date') input-error @enderror" 
                                    required
                                >
                                @error('start_date')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">End Date</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="end_date" 
                                    value="{{ old('end_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="input input-bordered w-full @error('end_date') input-error @enderror" 
                                    required
                                >
                                @error('end_date')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Reason</span>
                            </label><br>
                            <textarea 
                                name="reason" 
                                class="textarea textarea-bordered h-24 @error('reason') textarea-error @enderror" 
                                placeholder="Enter reason for leave"
                                required
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control mt-4">
                            <button type="submit" class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] text-white">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <span>You do not have any available leave balance to submit a request.</span>
            </div>
        @endif

        <!-- Section 3: My Leave Requests History -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">My Leave Requests</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->leave_type }}</td>
                                    <td>{{ $request->start_date->format('M d, Y') }}</td>
                                    <td>{{ $request->end_date->format('M d, Y') }}</td>
                                    <td>{{ $request->number_of_days }}</td>
                                    <td><x-status-badge :status="$request->status" /></td>
                                    <td>
                                        @if($request->isPending())
                                            <form action="{{ route('request.cancel', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-error hover:text-error/70 transition-colors">Cancel</button>
                                            </form>
                                        @else
                                            <span class="text-base-content/50">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-base-content/60">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($requests->hasPages())
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
        </div>
    </div>
</x-layout>

