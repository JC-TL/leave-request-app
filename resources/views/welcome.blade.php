<div class="flex justify-center mt-20">
    <a href="/">
        <img src="{{ asset('images/logo.png') }}" class="h-12 md:h-50   " alt="Logo">
    </a>
</div>
<x-layout>
    <br><br>
    
    <div class="card bg-base-100 shadow-sm border border-base-300">
    <div class="card-body p-6">
        <h2 class="text-3xl text-[#1C96E1] font-semibold text-center mt-2 mb-1">Welcome</h2>
        <p class="text-sm text-[#00194F] text-center mb-4">
            Sign in using your company account to continue.
        </p>

        @if ($errors->any())
            <div class="alert alert-error mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf

            <input 
                type="email" 
                name="email" 
                placeholder="Enter Email" 
                value="{{ old('email') }}"
                required
                class="input input-bordered w-full @error('email') input-error @enderror"
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Enter Password"    
                required
                class="input input-bordered w-full @error('password') input-error @enderror"
            >

            <button
                class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] w-full text-white">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <div class="inline-flex items-center gap-1.5 justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <p>Sign-in issues?</p>
            </div>

            <a href="{{ route('contact-admin') }}" class="text-blue-700 hover:underline block mt-1">
                Submit a Ticket
            </a>
        </div>
    </div>
</div>

</x-layout> 