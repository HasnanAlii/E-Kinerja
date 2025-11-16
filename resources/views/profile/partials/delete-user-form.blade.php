<section class="space-y-6 bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    {{-- HEADER --}}
    <header class="flex items-start gap-3">
        <span class="p-3 bg-red-100 text-red-600 rounded-full">
            <i data-feather="alert-triangle" class="w-6 h-6"></i>
        </span>

        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Hapus Akun
            </h2>

            <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                Menghapus akun akan menghapus semua data Anda secara permanen. 
                Pastikan Anda telah menyimpan data penting sebelum melanjutkan.
            </p>
        </div>
    </header>

        <div class="flex items-center gap-4 justify-center">
            <button 
                x-data 
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 
                    text-white rounded-lg shadow text-sm font-medium">
                <i data-feather="trash-2" class="w-4 h-4"></i>
                Hapus Akun
            </button>
        </div>



    {{-- MODAL --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>

        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            {{-- MODAL HEADER --}}
            <div class="flex items-start gap-3">
                <span class="p-3 bg-red-100 text-red-600 rounded-full">
                    <i data-feather="alert-octagon" class="w-6 h-6"></i>
                </span>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Konfirmasi Penghapusan Akun
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                        Setelah akun dihapus, semua data akan hilang dan tidak dapat dipulihkan.
                        Masukkan kata sandi untuk melanjutkan.
                    </p>
                </div>
            </div>

            {{-- INPUT PASSWORD --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Kata Sandi
                </label>

                <div class="relative">
                    <i data-feather="lock" class="w-4 h-4 text-gray-500 absolute left-3 top-3"></i>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Masukkan kata sandi Anda"
                        class="w-full pl-10 rounded-lg border-gray-300 shadow-sm 
                               focus:border-red-500 focus:ring-red-500"
                    />
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-end gap-3">

                {{-- CANCEL --}}
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 
                               hover:bg-gray-300 text-gray-700 rounded-lg shadow-sm text-sm font-medium">
                    <i data-feather="x-circle" class="w-4 h-4"></i>
                    Batal
                </button>

                {{-- DELETE --}}
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 
                               text-white rounded-lg shadow-sm text-sm font-medium">
                    <i data-feather="trash" class="w-4 h-4"></i>
                    Hapus Akun
                </button>
            </div>
        </form>

    </x-modal>

</section>
