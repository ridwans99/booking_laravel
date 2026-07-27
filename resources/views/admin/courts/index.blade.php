<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola lapangan</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.courts.store') }}"
              class="bg-white rounded-lg shadow p-4 mb-6 flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Nama lapangan</label>
                <input type="text" name="name" required class="w-full rounded-md border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Harga/jam</label>
                <input type="number" name="price_per_hour" required class="w-full rounded-md border-gray-300 text-sm">
            </div>
            <button class="px-4 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                Tambah
            </button>
        </form>

        <div class="bg-white rounded-lg shadow divide-y">
            @foreach ($courts as $court)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $court->name }}</p>
                        <p class="text-sm text-gray-500">
                            Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('admin.courts.update', $court) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $court->is_active ? 0 : 1 }}">
                            <button class="text-xs {{ $court->is_active ? 'text-gray-500' : 'text-emerald-600' }} hover:underline">
                                {{ $court->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.courts.destroy', $court) }}"
                              onsubmit="return confirm('Hapus lapangan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
