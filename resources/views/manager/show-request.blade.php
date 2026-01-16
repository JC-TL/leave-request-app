<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('manager.dashboard') }}" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
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
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Request Details</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4">
        <div class="space-y-6">
            <!-- Back Button -->
            <div class="flex justify-start mb-4">
                <a href="{{ route('manager.dashboard') }}" class="btn bg-[#1C96E1] border-[#1EA1F1] text-white hover:bg-[#00194F] hover:border-[#1C96E1]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
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
                        <h2 class="card-title text-2xl text-[#1C96E1] mb-6">Actions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Approve Form -->
                            <div class="space-y-4">
                                <div>
                                    <form action="{{ route('manager.approve-request', $leaveRequest->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Comment (Optional)</span>
                                            </label>
                                            <textarea 
                                                name="comment" 
                                                class="textarea textarea-bordered h-24 w-full" 
                                                placeholder="Add an optional comment..."
                                            ></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Approve Request
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Reject Form -->
                            <div class="space-y-4">
                                <div>
                                    <form action="{{ route('manager.reject-request', $leaveRequest->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Reason <span class="text-error">*</span></span>
                                            </label>
                                            <textarea 
                                                name="reason" 
                                                class="textarea textarea-bordered h-24 @error('reason') textarea-error @enderror w-full" 
                                                placeholder="Please provide a reason for rejection..."
                                                required
                                            >{{ old('reason') }}</textarea>
                                            @error('reason')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-error w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Reject Request
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>This request has already been {{ $leaveRequest->getStatusLabel() }}.</span>
                </div>
            @endif
        </div>
    </div>
</x-layout>

