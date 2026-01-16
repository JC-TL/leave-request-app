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
                            <a href="{{ route('hr.policies') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125V8.25zm6.75 0v8.25m0-8.25h3.375c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125H16.5V8.25z" />
                                </svg>
                                Policies
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.employees') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Employees
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
        <h2 class="flex justify-center mt-5 text-3xl text-[#1C96E1] font-semibold">Add New Employee</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4">
        <div class="space-y-6">
            <!-- Back Button -->
            <div class="flex justify-start mb-4">
                <a href="{{ route('hr.employees') }}" class="btn bg-[#1C96E1] border-[#1EA1F1] text-white hover:bg-[#00194F] hover:border-[#1C96E1]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Employees
                </a>
            </div>

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
                    <h2 class="card-title text-2xl text-[#1C96E1] mb-6">Employee Information</h2>
                    <form action="{{ route('hr.store-employee') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="form-control">
                                <label class="label" for="name">
                                    <span class="label-text font-medium">Full Name <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name"
                                    name="name" 
                                    value="{{ old('name') }}"
                                    class="input input-bordered w-full @error('name') input-error @enderror" 
                                    required
                                    aria-describedby="@error('name') error-name @enderror"
                                >
                                @error('name')
                                    <label class="label" id="error-name">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-control">
                                <label class="label" for="email">
                                    <span class="label-text font-medium">Email Address <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email"
                                    name="email" 
                                    value="{{ old('email') }}"
                                    class="input input-bordered w-full @error('email') input-error @enderror" 
                                    required
                                    aria-describedby="@error('email') error-email @enderror"
                                >
                                @error('email')
                                    <label class="label" id="error-email">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-control">
                                <label class="label" for="password">
                                    <span class="label-text font-medium">Password <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    class="input input-bordered w-full @error('password') input-error @enderror" 
                                    required
                                    minlength="8"
                                    aria-describedby="@error('password') error-password @enderror"
                                >
                                @error('password')
                                    <label class="label" id="error-password">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-control">
                                <label class="label" for="password_confirmation">
                                    <span class="label-text font-medium">Confirm Password <span class="text-error">*</span></span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation"
                                    name="password_confirmation" 
                                    class="input input-bordered w-full" 
                                    required
                                    minlength="8"
                                >
                            </div>

                            <!-- Role -->
                            <div class="form-control">
                                <label class="label" for="role">
                                    <span class="label-text font-medium">Role <span class="text-error">*</span></span>
                                </label>
                                <select 
                                    id="role"
                                    name="role" 
                                    class="select select-bordered w-full @error('role') select-error @enderror" 
                                    required
                                    aria-describedby="@error('role') error-role @enderror"
                                >
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <label class="label" id="error-role">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="form-control">
                                <label class="label" for="department_id">
                                    <span class="label-text font-medium">Department <span class="text-error">*</span></span>
                                </label>
                                <select 
                                    id="department_id"
                                    name="department_id" 
                                    class="select select-bordered w-full @error('department_id') select-error @enderror" 
                                    required
                                    aria-describedby="@error('department_id') error-department @enderror"
                                >
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <label class="label" id="error-department">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Manager (only for employees) -->
                            <div class="form-control" id="manager-field" style="display: none;">
                                <label class="label" for="manager_id">
                                    <span class="label-text font-medium">Manager</span>
                                </label>
                                <select 
                                    id="manager_id"
                                    name="manager_id" 
                                    class="select select-bordered w-full @error('manager_id') select-error @enderror" 
                                    aria-describedby="@error('manager_id') error-manager @enderror"
                                >
                                    <option value="">Select Manager (Optional)</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <label class="label" id="error-manager">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Leave balances will be automatically initialized based on current policies.</span>
                        </div>

                        <div class="flex justify-end gap-4 pt-4">
                            <a href="{{ route('hr.employees') }}" class="btn border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">
                                Cancel
                            </a>
                            <button type="submit" class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide manager field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const managerField = document.getElementById('manager-field');
            const managerSelect = document.getElementById('manager_id');
            
            if (this.value === 'employee') {
                managerField.style.display = 'block';
            } else {
                managerField.style.display = 'none';
                managerSelect.value = ''; // Clear selection when hidden
            }
        });

        // Check on page load if role is already selected
        window.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect.value === 'employee') {
                document.getElementById('manager-field').style.display = 'block';
            }
        });
    </script>
</x-layout>
