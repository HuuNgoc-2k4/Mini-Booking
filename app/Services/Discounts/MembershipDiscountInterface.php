<?php

namespace App\Services\Discounts;

interface MembershipDiscountInterface
{
    public function calculateDiscount(int $basePrice): int;
}
