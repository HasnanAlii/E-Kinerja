<x-guest-layout>
            <div class="px-8 pt-8 pb-6 text-center">
         
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                    Selamat Datang Kembali
                </h2>
                <p class="text-sm text-gray-500 mt-2">
                    Masuk ke Sistem E-Kinerja untuk melanjutkan.
                </p>
            </div>

            <div class="px-8">
                <x-auth-session-status class="mb-4" :status="session('status')" />
            </div>

            <form method="POST" action="{{ route('login') }}" class="px-8 pb-8 space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" :value="'Alamat Email'" class="text-gray-700 font-medium" />
                    <div class="relative mt-1.5">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="mail" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <x-text-input id="email" 
                                      class="block w-full pl-10 py-2.5 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                      type="email" name="email" :value="old('email')"
                                      required autofocus autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="'Kata Sandi'" class="text-gray-700 font-medium" />
                    <div class="relative mt-1.5">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <x-text-input id="password" 
                                      class="block w-full pl-10 py-2.5 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                      type="password" name="password"
                                      required autocomplete="current-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer" 
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <button type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-95">
                    Masuk Sekarang
                    <i data-feather="arrow-right" class="ml-2 w-4 h-4"></i>
                </button>

            </form>
            
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} Sistem E-Kinerja. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>