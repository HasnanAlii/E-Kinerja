<x-app-layout>
    <div class="py-12 min-h-screen">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-2xl p-8 text-white mb-8 shadow-lg">
                <h2 class="text-3xl font-bold">Dashboard {{ ucfirst($role === 'pegawai' ? 'karyawan' : $role) }}</h2>
                <p class="mt-2 text-indigo-100">Selamat datang, {{ Auth::user()->name }}. Monitoring kehadiran karyawan PT Pou Yuen.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                @foreach ($stats as [$label, $value, $icon])
                    <div class="bg-white p-6 rounded-2xl border shadow-sm flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">{{ $label }}</p>
                            <p class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $value }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600"><i data-feather="{{ $icon }}" class="w-7 h-7"></i></div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl shadow border p-5 max-w-xl">
                <h3 class="font-bold text-gray-800 mb-4">Menu Pintasan</h3>
                <div class="space-y-3">
                    @foreach ($links as [$t, $d, $url, $icon])
                        <a href="{{ $url }}" class="flex items-center p-4 border rounded-xl hover:border-indigo-500 transition">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg"><i data-feather="{{ $icon }}" class="w-6 h-6"></i></div>
                            <div class="ml-4"><h4 class="font-bold text-gray-800">{{ $t }}</h4><p class="text-xs text-gray-500">{{ $d }}</p></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
