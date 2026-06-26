@extends('layouts.app')

@section('title', 'Sign Up - ActiveCourt')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap');
    
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-inter { font-family: 'Inter', sans-serif; }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .input-glow:focus {
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }
</style>

<div class="min-h-[calc(100vh-4rem)] bg-[#F8FAFC] relative overflow-hidden font-inter py-10 lg:py-16 flex items-center">
    <!-- Subtle Background Decorations -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-[#EFF6FF] rounded-full blur-3xl opacity-80 mix-blend-multiply"></div>
        <div class="absolute top-[60%] -right-[10%] w-[40%] h-[60%] bg-[#EFF6FF] rounded-full blur-3xl opacity-80 mix-blend-multiply"></div>
        
        <!-- Small floating geometric shapes -->
        <div class="absolute top-[20%] right-[15%] w-4 h-4 rounded-full bg-[#60A5FA]/30"></div>
        <div class="absolute bottom-[25%] left-[20%] w-6 h-6 rotate-45 border-2 border-[#F97316]/30"></div>
        <div class="absolute top-[40%] left-[5%] w-3 h-3 rounded-full bg-[#22C55E]/30"></div>
    </div>

    <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="bg-white rounded-[24px] shadow-[0_20px_50px_rgba(37,99,235,0.06)] overflow-hidden border border-[#E2E8F0] flex flex-col lg:flex-row transform transition-all duration-500 hover:shadow-[0_30px_60px_rgba(37,99,235,0.1)]">
            
            <!-- Left Side: Illustration -->
            <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-[#2563EB] to-[#1E3A8A] p-12 flex-col justify-between relative overflow-hidden text-white group">
                <!-- Floating Glass Circles -->
                <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
                <div class="absolute bottom-20 -left-10 w-56 h-56 bg-white/10 rounded-full blur-2xl group-hover:translate-x-6 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <h2 class="font-poppins text-4xl lg:text-[44px] font-bold leading-tight mb-4 tracking-tight">Play More.<br>Book Easier.</h2>
                    <p class="text-[#EFF6FF] text-lg max-w-sm leading-relaxed opacity-90">Join thousands of players who book sports facilities through ActiveCourt.</p>
                </div>

                <!-- Decorative elements -->
                <div class="relative z-10 flex-1 flex items-center justify-center py-12">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <div class="absolute top-[10%] left-[10%] text-4xl animate-[bounce_3s_infinite]" style="animation-duration: 3.5s;">🏸</div>
                        <div class="absolute bottom-[15%] right-[10%] text-4xl animate-[bounce_3s_infinite]" style="animation-duration: 4s; animation-delay: 1s;">⚽</div>
                        <div class="absolute top-[40%] right-[5%] text-4xl animate-[bounce_3s_infinite]" style="animation-duration: 3s; animation-delay: 0.5s;">🏀</div>
                        <div class="absolute bottom-[25%] left-[5%] text-4xl animate-[bounce_3s_infinite]" style="animation-duration: 4.5s; animation-delay: 1.5s;">🎾</div>
                        
                        <!-- Main center illustration/abstract graphic -->
                        <div class="w-52 h-52 rounded-full border border-white/20 flex items-center justify-center relative shadow-[0_0_40px_rgba(255,255,255,0.1)]">
                            <div class="w-36 h-36 rounded-full bg-white/10 glass-effect flex items-center justify-center">
                                <i class="fas fa-layer-group text-[72px] text-white shadow-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-2 gap-4 mt-auto">
                    <div class="glass-effect p-5 rounded-[20px] transform transition duration-300 hover:-translate-y-1 hover:bg-white/20">
                        <div class="text-[32px] font-poppins font-bold text-white mb-0.5 leading-none">500+</div>
                        <div class="text-[#EFF6FF] text-sm font-medium">Sports Venues</div>
                    </div>
                    <div class="glass-effect p-5 rounded-[20px] transform transition duration-300 hover:-translate-y-1 hover:bg-white/20">
                        <div class="text-[32px] font-poppins font-bold text-white mb-0.5 leading-none">50K+</div>
                        <div class="text-[#EFF6FF] text-sm font-medium">Members</div>
                    </div>
                    <div class="glass-effect p-5 rounded-[20px] transform transition duration-300 hover:-translate-y-1 hover:bg-white/20 col-span-2 flex items-center justify-between">
                        <div>
                            <div class="text-[32px] font-poppins font-bold text-white mb-0.5 leading-none">20+</div>
                            <div class="text-[#EFF6FF] text-sm font-medium">Cities across Indonesia</div>
                        </div>
                        <i class="fas fa-map-marked-alt text-3xl text-white/50"></i>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full lg:w-7/12 p-8 sm:p-12 lg:px-16 lg:py-14 flex flex-col justify-center bg-white relative">
                <!-- Header -->
                <div class="mb-8 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-2 text-2xl font-poppins font-extrabold text-[#2563EB] mb-6">
                        <i class="fas fa-layer-group text-[#2563EB]"></i> ActiveCourt
                    </div>
                    <h3 class="font-poppins text-[32px] font-bold text-[#1E293B] mb-2 tracking-tight">Create Your Account</h3>
                    <p class="text-[#64748B] text-[15px]">Create an account and start booking your favorite sports venues.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label for="name_users" class="block text-sm font-medium text-[#1E293B] mb-2">Full Name</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-user text-[15px]"></i>
                                </span>
                                <input id="name_users" type="text" name="name_users" value="{{ old('name_users') }}" required autofocus
                                    class="w-full pl-11 pr-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none @error('name_users') border-red-500 @enderror"
                                    placeholder="John Doe">
                            </div>
                            @error('name_users') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-[#1E293B] mb-2">Username</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-at text-[15px]"></i>
                                </span>
                                <input id="username" type="text" name="username" value="{{ old('username') }}" required
                                    class="w-full pl-11 pr-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none @error('username') border-red-500 @enderror"
                                    placeholder="johndoe123">
                            </div>
                            @error('username') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#1E293B] mb-2">Email Address</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-envelope text-[15px]"></i>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full pl-11 pr-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none @error('email') border-red-500 @enderror"
                                    placeholder="john@example.com">
                            </div>
                            @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#1E293B] mb-2">Phone Number</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-phone text-[15px]"></i>
                                </span>
                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                    class="w-full pl-11 pr-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none @error('phone') border-red-500 @enderror"
                                    placeholder="081234567890">
                            </div>
                            @error('phone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#1E293B] mb-2">Password</label>
                            <div class="relative group" x-data="{ show: false }">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-lock text-[15px]"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                    class="w-full pl-11 pr-12 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none @error('password') border-red-500 @enderror"
                                    placeholder="Min. 8 characters">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#64748B] hover:text-[#1E293B] transition-colors focus:outline-none">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-[#1E293B] mb-2">Confirm Password</label>
                            <div class="relative group" x-data="{ show: false }">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#64748B] group-focus-within:text-[#2563EB] transition-colors">
                                    <i class="fas fa-lock text-[15px]"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                    class="w-full pl-11 pr-12 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-[16px] text-[#1E293B] text-[15px] focus:bg-white focus:border-[#2563EB] focus:ring-0 input-glow transition-all duration-300 outline-none"
                                    placeholder="Repeat password">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#64748B] hover:text-[#1E293B] transition-colors focus:outline-none">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" class="w-full bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#F97316] hover:to-[#ea580c] text-white font-poppins font-semibold py-4 px-4 rounded-[16px] transition-all duration-300 transform hover:-translate-y-1 shadow-[0_8px_16px_rgba(37,99,235,0.25)] hover:shadow-[0_12px_24px_rgba(249,115,22,0.3)] flex items-center justify-center gap-2">
                            <span class="text-[15px]">Create Account</span>
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="relative mt-8 mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#E2E8F0]"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-4 text-[#64748B] text-sm">Or continue with</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 bg-white border border-[#E2E8F0] hover:border-[#2563EB] text-[#1E293B] font-medium py-3.5 px-4 rounded-[16px] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_4px_12px_rgba(37,99,235,0.05)]">
                        <svg class="w-[20px] h-[20px]" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </a>
                    <button type="button" class="flex items-center justify-center gap-3 bg-white border border-[#E2E8F0] hover:border-[#1E293B] text-[#1E293B] font-medium py-3.5 px-4 rounded-[16px] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_4px_12px_rgba(30,41,59,0.05)]">
                        <svg class="w-[22px] h-[22px]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.09 2.31-.86 3.5-.8 1.49.07 2.65.65 3.3 1.5-3.03 1.83-2.58 5.92.51 7.1-1.06 2.76-2.53 5.34-2.39 4.37zm-3.41-14.73c.18-2.66 2.18-4.57 4.54-4.55-.44 2.86-2.88 4.79-4.54 4.55z"/>
                        </svg>
                        Apple
                    </button>
                </div>

                <!-- Footer -->
                <p class="text-center text-[15px] text-[#64748B] mt-10">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-[#2563EB] hover:text-[#1E3A8A] transition-colors decoration-2 underline-offset-4 hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
