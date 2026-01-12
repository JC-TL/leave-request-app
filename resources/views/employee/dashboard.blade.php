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
        <h2 class="flex justify-center mt-5 text-3xl font-semibold">Employee Dashboard</h2>
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

        <!-- Section 1: Leave Balance Display -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Leave Balance</h2>
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
                    <h2 class="card-title text-2xl mb-4">Submit Leave Request</h2>
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
                            <button type="submit" class="btn btn-primary">Submit Request</button>
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
                <h2 class="card-title text-2xl mb-4">My Leave Requests</h2>
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
</x-layout>

