<section class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    {{-- HEADER --}}
    <header class="mb-6 flex items-start gap-3">
        <span class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
            <i data-feather="lock" class="w-6 h-6"></i>
        </span>

        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Ubah Kata Sandi
            </h2>

            <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                Demi keamanan akun, gunakan kata sandi yang kuat dan sulit ditebak.
            </p>
        </div>
    </header>

    {{-- FORM --}}
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        {{-- PASSWORD SEKARANG --}}
        <div>
            <label for="update_password_current_password" class="text-sm font-medium text-gray-700">
                Kata Sandi Saat Ini
            </label>

            <div class="relative">
                <i data-feather="unlock" class="w-4 h-4 text-gray-500 absolute left-3 top-3"></i>

                <input 
                    id="update_password_current_password" 
                    name="current_password" 
                    type="password" 
                    placeholder="Masukkan kata sandi lama"
                    class="pl-10 mt-1 block w-full rounded-lg border-gray-300 shadow-sm 
                           focus:border-indigo-500 focus:ring-indigo-500"
                    autocomplete="current-password"
                >
            </div>

            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- PASSWORD BARU --}}
        <div>
            <label for="update_password_password" class="text-sm font-medium text-gray-700">
                Kata Sandi Baru
            </label>

            <div class="relative">
                <i data-feather="key" class="w-4 h-4 text-gray-500 absolute left-3 top-3"></i>

                <input 
                    id="update_password_password" 
                    name="password" 
                    type="password" 
                    placeholder="Masukkan kata sandi baru"
                    class="pl-10 mt-1 block w-full rounded-lg border-gray-300 shadow-sm 
                           focus:border-indigo-500 focus:ring-indigo-500"
                    autocomplete="new-password"
                >
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div>
            <label for="update_password_password_confirmation" class="text-sm font-medium text-gray-700">
                Konfirmasi Kata Sandi Baru
            </label>

            <div class="relative">
                <i data-feather="repeat" class="w-4 h-4 text-gray-500 absolute left-3 top-3"></i>

                <input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    type="password"
                    placeholder="Ulangi kata sandi baru"
                    class="pl-10 mt-1 block w-full rounded-lg border-gray-300 shadow-sm 
                           focus:border-indigo-500 focus:ring-indigo-500"
                    autocomplete="new-password"
                >
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- TOMBOL --}}
        <div class="flex items-center gap-4 justify-center">
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 
                       text-white rounded-lg text-sm font-medium shadow transition">
                <i data-feather="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1 text-sm text-green-600 font-medium"
                >
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                    Kata sandi diperbarui.
                </p>
            @endif
        </div>
    </form>

</section>
