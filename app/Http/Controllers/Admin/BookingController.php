<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $bookings = Booking::with(['user', 'court'])
            ->whereDate('booking_date', $date)
            ->orderBy('start_time')
            ->get();

        return view('admin.bookings.index', compact('bookings', 'date'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Status booking diperbarui.');
    }
}
