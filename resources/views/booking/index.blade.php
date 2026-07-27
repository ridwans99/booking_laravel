<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Booking lapangan</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @error('start_time')
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                {{ $message }}
            </div>
        @enderror

        <form method="GET" class="mb-6 flex items-center gap-3">
            <label class="text-sm text-gray-600">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" min="{{ now()->toDateString() }}"
                   class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
        </form>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-3 py-2 text-left font-medium">Jam</th>
                        @foreach ($courts as $court)
                            <th class="px-3 py-2 text-left font-medium">{{ $court->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slots as $slot)
                        <tr class="border-t">
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ $slot['start'] }}&ndash;{{ $slot['end'] }}
                            </td>
                            @foreach ($courts as $court)
                                @php
                                    $isBooked = collect($bookings[$court->id] ?? [])
                                        ->contains(fn ($b) => substr($b->start_time, 0, 5) === $slot['start']);
                                    $isPast = \Carbon\Carbon::parse($date.' '.$slot['start'])->isPast();
                                @endphp
                                <td class="px-3 py-2">
                                    @if ($isBooked)
                                        <span class="inline-block w-full text-center rounded-md bg-red-50 text-red-500 text-xs py-2">
                                            Terisi
                                        </span>
                                    @elseif ($isPast)
                                        <span class="inline-block w-full text-center rounded-md bg-gray-50 text-gray-400 text-xs py-2">
                                            Lewat
                                        </span>
                                    @else
                                        <button type="button"
                                            @click="$dispatch('open-booking', {
                                                courtId: {{ $court->id }},
                                                courtName: '{{ $court->name }}',
                                                date: '{{ $date }}',
                                                start: '{{ $slot['start'] }}',
                                                end: '{{ $slot['end'] }}',
                                                price: {{ $court->price_per_hour }}
                                            })"
                                            class="w-full rounded-md bg-emerald-50 text-emerald-700 text-xs py-2 hover:bg-emerald-100">
                                            Tersedia
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div x-data="{ open: false, courtId: null, courtName: '', date: '', start: '', end: '', price: 0 }"
         @open-booking.window="
            open = true;
            courtId = $event.detail.courtId;
            courtName = $event.detail.courtName;
            date = $event.detail.date;
            start = $event.detail.start;
            end = $event.detail.end;
            price = $event.detail.price;
         "
         x-show="open" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm" @click.outside="open = false">
            <h3 class="text-lg font-semibold mb-1">Konfirmasi booking</h3>
            <p class="text-sm text-gray-500 mb-4">
                <span x-text="courtName"></span> &middot; <span x-text="date"></span> &middot;
                <span x-text="start"></span>&ndash;<span x-text="end"></span>
            </p>
            <p class="text-sm text-gray-700 mb-4">
                Harga: Rp <span x-text="price.toLocaleString('id-ID')"></span>
                <span class="text-gray-400">(bayar di tempat)</span>
            </p>
            <form method="POST" action="{{ route('booking.store') }}">
                @csrf
                <input type="hidden" name="court_id" :value="courtId">
                <input type="hidden" name="booking_date" :value="date">
                <input type="hidden" name="start_time" :value="start">
                <input type="hidden" name="end_time" :value="end">
                <label class="block text-sm text-gray-600 mb-1">Catatan (opsional)</label>
                <input type="text" name="notes" class="w-full rounded-md border-gray-300 text-sm mb-4"
                       placeholder="Contoh: sewa raket 2">
                <div class="flex justify-end gap-2">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm rounded-md text-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                        Booking sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
