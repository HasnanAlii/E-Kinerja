<x-guest-layout>

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">
            Lupa Kata Sandi
        </h2>
        <p class="mt-2 text-gray-600">
            Untuk mereset kata sandi akun Anda, silakan hubungi administrator.
        </p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <p class="text-gray-700 text-center leading-relaxed">
            Demi keamanan sistem, proses reset kata sandi hanya dapat dilakukan oleh administrator.
            <br><br>
            Silakan hubungi admin dan berikan informasi akun Anda untuk proses lebih lanjut.
        </p>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
               class="inline-block px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                Kembali ke Login
            </a>
        </div>
    </div>

</x-guest-layout>
