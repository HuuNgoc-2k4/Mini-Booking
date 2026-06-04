<?php

namespace App\Services\Discounts;

use App\Models\User;

class DiscountFactory
{
    public static function make(User $user): MembershipDiscountInterface
    {
        return match ($user->role) {
            'admin', 'vip' => new VipMemberDiscount(),
            default        => new NormalMemberDiscount(),
        };
    }
}
