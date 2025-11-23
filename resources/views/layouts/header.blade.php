<script src="https://unpkg.com/alpinejs" defer></script>

<nav class="bg-white border-b shadow sticky top-0 z-50">
    <div class="px-10 mx-auto py-3 flex justify-between items-center">

        <div class="text-xl font-bold text-gray-700">
            {{-- {{ $title ?? 'Dashboard' }} --}}
        </div>

        <div class="flex items-center gap-6">

            <span class="px-3 py-1 text-xs font-semibold rounded-full
                @if(Auth::user()->hasRole('admin'))
                    bg-red-100 text-red-700
                @elseif(Auth::user()->hasRole('atasan'))
                    bg-indigo-100 text-indigo-700
                @else
                    bg-green-100 text-green-700
                @endif
            ">
                {{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}
            </span>

            <div x-data="notificationComponent()" class="relative">

                <button @click="toggleNotif()" class="relative focus:outline-none mt-2">
                    <i data-feather="bell" class="w-6 h-6 text-gray-600"></i>

                    <template x-if="unreadCount > 0">
                        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 rounded-full">
                            <span x-text="unreadCount"></span>
                        </span>
                    </template>
                </button>

                <div x-show="openNotif"
                     @click.outside="openNotif = false"
                     x-transition
                     class="absolute right-0 mt-3 w-72 bg-white shadow-lg rounded-lg border border-gray-200 z-50"
                >
                    <div class="p-3 border-b font-semibold">Notifikasi</div>

                    <div class="max-h-64 overflow-y-auto">

                        <template x-if="notifications.length === 0">
                            <div class="px-4 py-3 text-center text-gray-500 text-sm">
                                Tidak ada notifikasi
                            </div>
                        </template>

                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="px-4 py-3 hover:bg-gray-50">
                                <p class="text-sm text-gray-800 font-medium" x-text="notif.aktivitas"></p>
                                <p class="text-xs text-gray-500" x-text="timeAgo(notif.waktu)"></p>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

            <div class="relative" x-data="{ open: false }">

                <button @click="open = !open"
                        class="flex items-center gap-3 focus:outline-none">

                    @if (Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                             class="w-10 h-10 rounded-full object-cover border shadow">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-300 border flex items-center justify-center shadow">
                            <i data-feather="user" class="w-5 h-5 text-gray-600"></i>
                        </div>
                    @endif

                    {{-- NAMA --}}
                    <span class="text-gray-700 font-semibold">
                        {{ Auth::user()->name }}
                    </span>

                    <i data-feather="chevron-down" class="w-4 h-4 text-gray-600"></i>
                </button>

                <div x-show="open"
                     @click.outside="open = false"
                     x-transition
                     class="absolute right-0 mt-3 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-2"
                >
                    <a href="{{ route('profil.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">
                        <i data-feather="user" class="w-4 h-4"></i>
                        Profil Saya
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">
                        <i data-feather="key" class="w-4 h-4"></i>
                        Ubah Kata Sandi
                    </a>

                    <div class="border-t my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 text-sm">
                            <i data-feather="log-out" class="w-4 h-4"></i>
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</nav>

<script>
function notificationComponent() {
    return {
        openNotif: false,
        notifications: [],
        unreadCount: 0,

        async loadNotif() {
            const res = await fetch('/notifications');
            const data = await res.json();
            this.notifications = data;

            this.unreadCount = data.filter(n => n.read_at === null).length;
        },

        async markAsRead() {
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            this.unreadCount = 0; 
        },

        async toggleNotif() {
            this.openNotif = !this.openNotif;
            if (this.openNotif) {
                await this.markAsRead();
            }
        },

        timeAgo(datetime) {
            return window.moment(datetime).fromNow();
        },

        init() {
            this.loadNotif();
        }
    }
}
</script>
