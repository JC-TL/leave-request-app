<div class="flex justify-center mt-20">
    <a href="/">
        <img src="{{ asset('images/logo.png') }}" class="h-12 md:h-50   " alt="Logo">
    </a>
</div>
<x-layout>
    <br><br>
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-6">
                <h2 class="text-3xl font-semibold text-center mt-2 mb-2">Welcome</h2>
                <p class="text-3x text-center mb-20">Sign in using your company account to continue.</p>

                @if ($errors->any())
                    <div class="alert alert-error mb-6">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
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

                    <button class="btn btn-primary btn-sm w-full">
                        Sign In
                    </button>
                </form> 
                <br><br>
                <div class="text-center">
                    <p >Sign-in issues?</p>
                    <a href="/register" class="text-blue-700 hover:underline"><p>Contact Admin</p></a>
                </div>
            </div>
        </div>
</x-layout> 