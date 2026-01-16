<div class="flex justify-center mt-10 mb-6">
    <a href="{{ route('login') }}">
        <img src="{{ asset('images/logo.png') }}" class="h-12 md:h-16" alt="Logo">
    </a>
</div>
<x-layout>
    <div class="card bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl text-[#1C96E1] font-semibold">Contact Admin</h2>
                <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
            
            <p class="text-sm text-[#00194F] mb-6">Having trouble signing in? Please submit a ticket below and our admin team will assist you.</p>

            @if(session('success'))
                <div class="alert alert-success mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact-admin.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Your Name <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Enter your full name"
                        value="{{ old('name') }}"
                        required
                        class="input input-bordered w-full @error('name') input-error @enderror"
                    >
                    @error('name')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Address <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        required
                        class="input input-bordered w-full @error('email') input-error @enderror"
                    >
                    @error('email')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Subject <span class="text-error">*</span></span>
                    </label>
                    <select 
                        name="subject" 
                        class="select select-bordered w-full @error('subject') select-error @enderror"
                        required
                    >
                        <option value="">Select an issue</option>
                        <option value="login_issue" {{ old('subject') == 'login_issue' ? 'selected' : '' }}>Cannot Sign In</option>
                        <option value="password_reset" {{ old('subject') == 'password_reset' ? 'selected' : '' }}>Password Reset Request</option>
                        <option value="account_access" {{ old('subject') == 'account_access' ? 'selected' : '' }}>Account Access Issue</option>
                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('subject')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Message <span class="text-error">*</span></span>
                    </label>
                    <textarea 
                        name="message" 
                        class="textarea textarea-bordered h-32 @error('message') textarea-error @enderror" 
                        placeholder="Please describe your issue in detail..."
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input 
                            type="checkbox" 
                            name="urgent" 
                            class="checkbox"
                            style="accent-color: #1C96E1;"
                            {{ old('urgent') ? 'checked' : '' }}
                        >
                        <span class="label-text">Mark as urgent</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="submit" class="btn bg-[#1C96E1] border-[#1EA1F1] hover:bg-[#00194F] hover:border-[#1C96E1] text-white flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Submit Ticket
                    </button>
                    <a href="{{ route('login') }}" class="btn border-[#1EA1F1] text-[#1C96E1] hover:bg-[#00194F] hover:border-[#1C96E1] hover:text-white flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
