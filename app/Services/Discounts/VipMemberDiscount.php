<?php

namespace App\Services\Discounts;

class VipMemberDiscount implements MembershipDiscountInterface
{
    public function calculateDiscount(int $basePrice): int
    {
        return (int)($basePrice * 0.1);
    }
}
