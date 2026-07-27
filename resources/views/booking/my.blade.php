<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Booking saya</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow divide-y">
            @forelse ($bookings as $booking)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $booking->court->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }} &middot;
                            {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span @class([
                            'text-xs px-2 py-1 rounded-full',
                            'bg-emerald-50 text-emerald-700' => $booking->status === 'confirmed',
                            'bg-gray-100 text-gray-600' => $booking->status === 'completed',
                            'bg-red-50 text-red-600' => $booking->status === 'cancelled',
                        ])>
                            {{ ucfirst($booking->status) }}
                        </span>

                        @if ($booking->status === 'confirmed')
                            <form method="POST" action="{{ route('booking.cancel', $booking) }}"
                                  onsubmit="return confirm('Batalkan booking ini?')">
                                @csrf
                                @method('PATCH')
                                <button class="text-xs text-red-500 hover:underline">Batalkan</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="p-4 text-sm text-gray-500">Belum ada booking.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
