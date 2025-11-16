<div x-data="{ open: false, url: '', type: '', title: '', placeholder: '' }" x-cloak>

    {{-- BACKDROP --}}
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 bg-black/40 z-40"></div>

    {{-- MODAL --}}
    <div x-show="open" x-transition
         class="fixed z-50 top-1/2 left-1/2 w-full max-w-md bg-white rounded-lg shadow-xl p-6 
                -translate-x-1/2 -translate-y-1/2">

        <h3 class="text-xl font-bold text-gray-900" x-text="title"></h3>
        <p class="text-sm text-gray-600 mb-4">Harap periksa sebelum melanjutkan.</p>

        <form :action="url" method="POST" class="space-y-4">
            @csrf

            <template x-if="type !== 'approve'">
                <div>
                    <label class="text-sm font-medium">Catatan</label>
                    <input type="text" name="komentar_atasan"
                           class="mt-1 w-full border rounded-lg p-2"
                           x-bind:placeholder="placeholder">
                </div>
            </template>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                        @click="open=false"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                    Batal
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded-lg text-white shadow"
                        :class="{
                            'bg-green-600 hover:bg-green-700': type==='approve',
                            'bg-red-600 hover:bg-red-700': type==='reject',
                            'bg-yellow-500 hover:bg-yellow-600': type==='revisi'
                        }">
                    Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(type, actionUrl) {
        const modal = document.querySelector('[x-data]');
        modal.__x.$data.open = true;
        modal.__x.$data.url = actionUrl;
        modal.__x.$data.type = type;

        if (type === 'approve') {
            modal.__x.$data.title = "Setujui Aktivitas?";
            modal.__x.$data.placeholder = "";
        } else if (type === 'reject') {
            modal.__x.$data.title = "Tolak Aktivitas?";
            modal.__x.$data.placeholder = "Masukkan alasan penolakan...";
        } else {
            modal.__x.$data.title = "Minta Revisi?";
            modal.__x.$data.placeholder = "Masukkan catatan revisi...";
        }
    }
</script>
