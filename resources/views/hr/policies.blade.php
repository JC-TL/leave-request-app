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
                            <a href="{{ route('hr.policies') }}" class="active">
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
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Leave Policies</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4">
        <div class="space-y-6">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Errors found:</h3>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-2xl text-[#1C96E1] mb-6">Leave Policies</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="font-semibold">Leave Type</th>
                                    <th class="font-semibold">Annual Entitlement (Days)</th>
                                    <th class="font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                    <tr>
                                        <td class="font-medium">{{ $policy->leave_type }}</td>
                                        <td>{{ number_format($policy->annual_entitlement, 1) }}</td>
                                        <td class="text-right">
                                            <button 
                                                type="button"
                                                onclick="document.getElementById('edit-modal-{{ $policy->id }}').showModal()" 
                                                class="btn btn-sm border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white"
                                                aria-label="Edit {{ $policy->leave_type }} policy"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21h-4.5A2.25 2.25 0 019 18.75V14.25m9-4.125V18.75A2.25 2.25 0 0118.75 21H15m-3-9l-3 3m0 0l3 3m-3-3h7.5" />
                                                </svg>
                                                Edit
                                            </button>
                                            
                                            <dialog id="edit-modal-{{ $policy->id }}" class="modal" role="dialog" aria-labelledby="modal-title-{{ $policy->id }}">
                                                <div class="modal-box">
                                                    <h3 id="modal-title-{{ $policy->id }}" class="font-bold text-lg mb-4">Edit Policy: {{ $policy->leave_type }}</h3>
                                                    <form action="{{ route('hr.update-policy', $policy->id) }}" method="POST" id="edit-form-{{ $policy->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="form-control mb-4">
                                                            <label class="label" for="annual_entitlement-{{ $policy->id }}">
                                                                <span class="label-text font-medium">Annual Entitlement (Days)</span>
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="annual_entitlement-{{ $policy->id }}"
                                                                name="annual_entitlement" 
                                                                value="{{ old('annual_entitlement', $policy->annual_entitlement) }}"
                                                                min="0"
                                                                max="365"
                                                                step="0.5"
                                                                class="input input-bordered w-full @error('annual_entitlement') input-error @enderror" 
                                                                required
                                                                aria-describedby="@error('annual_entitlement') error-{{ $policy->id }} @enderror"
                                                            >
                                                            @error('annual_entitlement')
                                                                <label class="label" id="error-{{ $policy->id }}">
                                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                                </label>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-action">
                                                            <button 
                                                                type="button" 
                                                                onclick="document.getElementById('edit-modal-{{ $policy->id }}').close()" 
                                                                class="btn border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white"
                                                            >
                                                                Cancel
                                                            </button>
                                                            <button 
                                                                type="submit" 
                                                                class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white"
                                                            >
                                                                Update Policy
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <form method="dialog" class="modal-backdrop">
                                                    <button type="button" aria-label="Close modal">close</button>
                                                </form>
                                            </dialog>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-base-content/60 py-8">
                                            <p class="text-lg">No policies found.</p>
                                        </td>
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

