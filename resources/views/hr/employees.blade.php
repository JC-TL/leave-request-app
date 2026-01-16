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
                            <a href="{{ route('hr.employees') }}" class="active">
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
        <div class="text-center mt-5 mb-6">
            <h2 class="text-3xl text-[#1C96E1] font-semibold">Employee Management</h2>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4">
        <div class="space-y-6">
            <!-- Back Button -->
            <div class="flex justify-start mb-4">
                <a href="{{ route('hr.dashboard') }}" class="btn bg-[#1C96E1] border-[#1EA1F1] text-white hover:bg-[#00194F] hover:border-[#1C96E1]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
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
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="font-semibold">Name</th>
                                    <th class="font-semibold">Email</th>
                                    <th class="font-semibold">Role</th>
                                    <th class="font-semibold">Department</th>
                                    <th class="font-semibold">Manager</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td class="font-medium">{{ $employee->name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>
                                            <span class="badge {{ $employee->role === 'dept_manager' ? 'badge-primary' : 'badge-ghost' }}">
                                                {{ ucfirst(str_replace('_', ' ', $employee->role)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($employee->department)
                                                <span class="badge badge-outline" style="border-color: {{ $employee->department->color }}; color: {{ $employee->department->color }}">
                                                    {{ $employee->department->name }}
                                                </span>
                                            @else
                                                <span class="text-base-content/60">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->manager)
                                                {{ $employee->manager->name }}
                                            @else
                                                <span class="text-base-content/60">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-base-content/60 py-8">
                                            <p class="text-lg">No employees found.</p>
                                            <a href="{{ route('hr.create-employee') }}" class="btn btn-sm mt-4 bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">
                                                Add First Employee
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($employees->hasPages())
                        <div class="mt-4">
                            {{ $employees->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Employee Button -->
            <div class="flex justify-center mt-6">
                <a href="{{ route('hr.create-employee') }}" class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Employee
                </a>
            </div>
        </div>
    </div>
</x-layout>
