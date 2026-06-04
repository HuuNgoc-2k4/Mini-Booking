<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\User;
use App\Services\Discounts\DiscountFactory;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService {
    public function handleBooking(int $userId, int $slotId) {
        return DB::transaction(function () use ($userId, $slotId) {
            $user = User::lockForUpdate()->findOrFail($userId);
            $slot = Slot::lockForUpdate()->findOrFail($slotId);

            if ($slot->booked_seats >= $slot->max_seats) {
                throw new Exception('Đã hết chỗ.');
            }

            $discountStrategy = DiscountFactory::make($user);

            $discountAmount = $discountStrategy->calculateDiscount($slot->price);

            $finalPrice = max(0, $slot->price - $discountAmount);

            if ($user->balance < $finalPrice) {
                throw new Exception('Số dư không đủ để thanh toán suất này.');
            }

            $user->decrement('balance', $finalPrice);
            $slot->increment('booked_seats');

            return Booking::create([
                'user_id' => $user->id,
                'slot_id' => $slot->id,
                'status' => 'confirmed'
            ]);
        });
    }
}
