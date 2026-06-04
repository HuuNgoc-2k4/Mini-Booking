<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SlotController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request) {
        $slots = Cache::remember('all_slots_cache', 60, function () {
            return Slot::all()->toArray();
        });

        $currentUser = auth()->user();
        $editingSlotId = $request->query('edit_id');

        return view('Slots.index', [
            'slots' => $slots,
            'currentUser' => $currentUser,
            'editingSlotId' => $editingSlotId,
        ]);
    }

    // Đặt chỗ cho một slot
    public function book(Request $request, $slotId) {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt chỗ!');
        }

        try {
            $this->bookingService->handleBooking(auth()->id(), $slotId);

            Cache::forget('all_slots_cache');

            return redirect()->route('slots.index')->with('success', 'Đặt vé thành công');
        } catch (\Exception $e) {
            return redirect()->route('slots.index')->with('error', $e->getMessage());
        }
    }

    //Tạo mới booking
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'max_seats' => 'required|integer',
            'price' => 'required|integer',
        ]);

        Slot::create($request->all());

        Cache::forget('all_slots_cache');

        return redirect()->route('slots.index')->with('success', 'Đã thêm suất đặt chỗ mới!');
    }

    //Sửa booking
    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required',
            'max_seats' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $slot = Slot::findOrFail($id);
        $slot->update($request->only(['title', 'max_seats', 'price']));

        Cache::forget('all_slots_cache');

        return redirect()->route('slots.index')->with('success', 'Đã cập nhật thông tin thành công!');
    }
}
