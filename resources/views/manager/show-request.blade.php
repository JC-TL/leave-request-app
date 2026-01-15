<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('manager.dashboard') }}" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
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
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Request Details</h2>
    </x-slot>

    <div class="space-y-6">
        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Employee Details -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Employee Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-base-content/60">Name</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Department</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Email</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->employee->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Request Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-base-content/60">Leave Type</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->leave_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Start Date</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">End Date</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->end_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Number of Days</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->number_of_days }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Submitted Date</p>
                        <p class="text-lg font-semibold">{{ $leaveRequest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-base-content/60">Current Balance</p>
                        <p class="text-lg font-semibold">
                            {{ $balance ? number_format($balance->getAvailableBalance(), 1) : '0' }} / 
                            {{ $balance ? number_format($balance->balance, 1) : '0' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-base-content/60">Reason</p>
                        <p class="text-lg">{{ $leaveRequest->reason }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Actions -->
        @if($leaveRequest->isPending())
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-2xl text-[#1C96E1] mb-4">Actions</h2>
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Approve Form -->
                        <div class="flex-1">
                            <form action="{{ route('manager.approve-request', $leaveRequest->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text">Comment (Optional)</span>
                                    </label>
                                    <textarea name="comment" class="textarea textarea-bordered h-20" placeholder="Optional comment"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-full">Approve</button>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div class="flex-1">
                            <form action="{{ route('manager.reject-request', $leaveRequest->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text">Reason (Required)</span>
                                    </label>
                                    <textarea name="reason" class="textarea textarea-bordered h-20 @error('reason') textarea-error @enderror" placeholder="Reason for rejection" required></textarea>
                                    @error('reason')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-error w-full">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <span>This request has already been {{ $leaveRequest->getStatusLabel() }}.</span>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('manager.dashboard') }}" class="btn border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</x-layout>

