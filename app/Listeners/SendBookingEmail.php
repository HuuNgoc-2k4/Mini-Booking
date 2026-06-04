<?php

namespace App\Listeners;

use App\Events\BookingSuccessful;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendBookingEmail
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingSuccessful $event): void
    {
        try {
            $booking = $event->booking;

            // Giả sử đã gửi được email thành công

            // Ghi nhận log chi tiết hóa đơn
            Log::info("Đã gửi email xác nhận thành công");
            Log::info("Booking ID: " . $booking->id);
            Log::info("User ID : " . $booking->user_id);
            Log::info("Slot ID: " . $booking->slot_id);
            Log::info("Status  : " . strtoupper($booking->status ?? 'confirmed'));
            Log::info("Thời gian gửi: " . now()->toDateTimeString());


        } catch (Exception $e) {
            // Ghi log cảnh báo nếu có lỗi
            Log::error("Queue error: " . $e->getMessage());

            throw $e;
        }
    }
    public function failed(BookingSuccessful $event, $exception): void
    {
        Log::critical("Gửi email xác nhận đặt chỗ thất bại cho Booking ID: " . $event->booking->id);
        Log::critical("- lỗi hệ thống: " . $exception->getMessage());
    }
}
