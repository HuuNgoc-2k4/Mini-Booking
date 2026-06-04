<?php

namespace App\Services\Discounts;

class NormalMemberDiscount implements MembershipDiscountInterface
{
    public function calculateDiscount(int $basePrice): int
    {
        return 0;
    }
}
