<x-guest-layout>

    <!-- STATUS LOGIN -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- JUDUL --}}
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center justify-center gap-2">
                <i data-feather="log-in" class="w-6 h-6 text-indigo-600"></i>
                Masuk ke Sistem E-Kinerja
            </h2>
            <p class="text-sm text-gray-600">
                Silakan masuk menggunakan akun yang telah terdaftar.
            </p>
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="'Email'" />
            <div class="relative">
                <i data-feather="mail" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                <x-text-input id="email" class="block mt-1 w-full pl-10" 
                              type="email" name="email" :value="old('email')"
                              placeholder="Masukkan email"
                              required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="'Kata Sandi'" />
            <div class="relative">
                <i data-feather="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                <x-text-input id="password" class="block mt-1 w-full pl-10"
                              type="password" name="password"
                              placeholder="Masukkan kata sandi"
                              required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                       name="remember">
                <span class="ml-2 text-sm text-gray-700">
                    Ingat saya
                </span>
            </label>
        </div>

        <!-- ACTION -->
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 underline"
                   href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif

            <x-primary-button class="ml-3">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <script>
        feather.replace();
    </script>

</x-guest-layout>
