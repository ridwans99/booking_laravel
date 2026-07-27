<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Halaman utama: grid ketersediaan lapangan per jam untuk tanggal terpilih.
     */
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $courts = Court::where('is_active', true)->orderBy('name')->get();
        $slots = $this->generateSlots();

        $bookings = Booking::whereDate('booking_date', $date)
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('court_id');

        return view('booking.index', compact('date', 'courts', 'slots', 'bookings'));
    }

    /**
     * Membuat booking baru. Status langsung "confirmed" karena bayar di tempat
     * saat kedatangan, bukan lewat payment gateway.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $conflict = Booking::where('court_id', $validated['court_id'])
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('status', 'confirmed')
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'start_time' => 'Slot ini baru saja dipesan orang lain. Silakan pilih slot lain.',
            ]);
        }

        Booking::create([
            'user_id' => $request->user()->id,
            'court_id' => $validated['court_id'],
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'confirmed',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('booking.my')
            ->with('success', 'Booking berhasil dibuat! Datang tepat waktu dan bayar langsung di tempat.');
    }

    /**
     * Daftar booking milik user yang sedang login.
     */
    public function my()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('court')
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate(10);

        return view('booking.my', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking dibatalkan.');
    }

    /**
     * Bangun daftar slot jam berdasarkan config/badminton.php
     */
    private function generateSlots(): array
    {
        $slots = [];
        $cursor = Carbon::createFromFormat('H:i', config('badminton.open_time'));
        $close = Carbon::createFromFormat('H:i', config('badminton.close_time'));
        $length = (int) config('badminton.slot_minutes');

        while ($cursor->copy()->addMinutes($length)->lte($close)) {
            $slots[] = [
                'start' => $cursor->format('H:i'),
                'end' => $cursor->copy()->addMinutes($length)->format('H:i'),
            ];
            $cursor->addMinutes($length);
        }

        return $slots;
    }
}
