<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    /**
     * Types of charges
     */
    const CHARGE_RECURRING = 1;
    const CHARGE_ONETIME = 2;
    const CHARGE_USAGE = 3;
    const CHARGE_CREDIT = 4;

    protected $guarded = ['id'];
}
