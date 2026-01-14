<x-layout>
    <x-slot name="navigation">
        <nav class="navbar bg-base-100 shadow-sm border-b border-base-300 px-6 py-4 w-full">
            <div class="flex-1">
                <a href="{{ route('hr.dashboard') }}" class="text-lg font-semibold"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 md:h-20"></a>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <a href="{{ route('hr.dashboard') }}" class="btn btn-sm btn-outline">Dashboard</a>
                <span class="text-sm">{{ auth()->user()->name }}</span>
                <span class="badge badge-primary">{{ ucfirst(auth()->user()->role) }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-base-content hover:text-base-content/70 transition-colors">Logout</button>
                </form>
            </div>
            @endauth
        </nav>
        <h2 class="flex justify-center mt-5 text-3xl font-semibold">Leave Policies</h2>
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

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('hr.dashboard') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Policies Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Leave Policies</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Annual Entitlement (Days)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($policies as $policy)
                                <tr>
                                    <td>{{ $policy->leave_type }}</td>
                                    <td>{{ $policy->annual_entitlement }}</td>
                                    <td>
                                        <button onclick="document.getElementById('edit-modal-{{ $policy->id }}').showModal()" class="btn btn-sm btn-primary btn-outline">Edit</button>
                                        
                                        <!-- Edit Modal -->
                                        <dialog id="edit-modal-{{ $policy->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg mb-4">Edit Policy: {{ $policy->leave_type }}</h3>
                                                <form action="{{ route('hr.update-policy', $policy->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-control mb-4">
                                                        <label class="label">
                                                            <span class="label-text">Annual Entitlement (Days)</span>
                                                        </label>
                                                        <input 
                                                            type="number" 
                                                            name="annual_entitlement" 
                                                            value="{{ old('annual_entitlement', $policy->annual_entitlement) }}"
                                                            min="0"
                                                            max="365"
                                                            class="input input-bordered w-full @error('annual_entitlement') input-error @enderror" 
                                                            required
                                                        >
                                                        @error('annual_entitlement')
                                                            <label class="label">
                                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                                            </label>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="button" onclick="document.getElementById('edit-modal-{{ $policy->id }}').close()" class="btn btn-outline">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <form method="dialog" class="modal-backdrop">
                                                <button>close</button>
                                            </form>
                                        </dialog>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-base-content/60">No policies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>

