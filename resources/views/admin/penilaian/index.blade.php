<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Penilaian Pegawai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 flex items-center bg-green-100 text-green-700 border border-green-300 rounded-lg text-base">
                    <i data-feather="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            <div class="px-6 py-6 border-b bg-gray-50 border-gray-100 rounded-t-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                
                {{-- LEFT SECTION: TITLE & ICON --}}
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm ring-1 ring-indigo-100">
                        <i data-feather="bar-chart-2" class="w-6 h-6"></i>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">
                            Laporan Penilaian Pegawai
                        </h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Rekap kinerja pegawai periode ini.
                        </p>
                    </div>
                </div>

                {{-- RIGHT SECTION: FILTER & ACTIONS --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    
                    {{-- Filter Form --}}
                    <form method="GET" class="flex items-center">
                        <div class="relative w-full md:w-auto">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-feather="filter" class="w-4 h-4"></i>
                            </div>
                            <select name="bidang_id" onchange="this.form.submit()" 
                                class="w-full md:w-64 pl-10 pr-8 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer appearance-none hover:bg-gray-100">
                                <option value="">Semua Bidang</option>
                                @foreach($bidang as $b)
                                    <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_bidang }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- Custom Arrow Icon (Optional, Tailwind biasanya sudah handle, tapi ini untuk memastikan) --}}
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </form>

                    {{-- Separator (Hidden on mobile) --}}
                    <div class="hidden sm:block h-8 w-px bg-gray-200"></div>

                    {{-- Action Button --}}
                    <button id="btn-validasi" 
                        class="group inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-300 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i data-feather="check-circle" class="w-4 h-4 mr-2 group-hover:text-indigo-100 transition-colors"></i>
                        <span class="font-medium text-sm">Validasi Laporan</span>
                    </button>

                </div>
            </div>

            <div class=" bg-white rounded-b-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Pegawai
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Periode
                                </th>
                                <th scope="col" class="px-6 py-3 ttext-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Predikat
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($data as $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base font-medium text-gray-900">
                                            {{ $row->pegawai->user->name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base text-gray-700">
                                            {{ $row->periode->nama_periode }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-base font-semibold text-gray-900">
                                            {{ number_format($row->nilai_total, 2) }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row->kategori == 'Sangat Baik')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Sangat Baik
                                            </span>
                                        @elseif($row->kategori == 'Baik')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Baik
                                            </span>
                                        @elseif($row->kategori == 'Cukup')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Cukup
                                            </span>
                                        @elseif($row->kategori == 'Kurang')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Kurang
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                                {{ $row->kategori }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-center">
                                        <a href="{{ route('admin.penilaian.show', $row->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                            <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-base text-gray-500">
                                        Belum ada data penilaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($data->hasPages())
                    <div class="mt-6 p-4 bg-gray-50 border-t rounded-b-lg">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById("btn-validasi").addEventListener("click", function () {
    Swal.fire({
        title: "Validasi Laporan?",
        text: "Pastikan seluruh laporan sudah benar sebelum divalidasi.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2563eb",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Validasi!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('admin.penilaian.validasi') }}";

        }
    });
});
</script>

</x-app-layout>
