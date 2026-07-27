<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola booking</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="mb-6 flex items-center gap-3">
            <label class="text-sm text-gray-600">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" class="rounded-md border-gray-300 text-sm"
                   onchange="this.form.submit()">
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-3 py-2 text-left font-medium">Jam</th>
                        <th class="px-3 py-2 text-left font-medium">Lapangan</th>
                        <th class="px-3 py-2 text-left font-medium">Pelanggan</th>
                        <th class="px-3 py-2 text-left font-medium">Status</th>
                        <th class="px-3 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                            </td>
                            <td class="px-3 py-2">{{ $booking->court->name }}</td>
                            <td class="px-3 py-2">
                                {{ $booking->user->name }}
                                <span class="text-gray-400">({{ $booking->user->phone ?? '-' }})</span>
                            </td>
                            <td class="px-3 py-2">{{ ucfirst($booking->status) }}</td>
                            <td class="px-3 py-2">
                                @if ($booking->status === 'confirmed')
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="text-xs text-emerald-600 hover:underline mr-2">Tandai selesai</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button class="text-xs text-red-500 hover:underline">Batalkan</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                Belum ada booking di tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
