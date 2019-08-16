<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the users that belong to the store.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'store_users');
    }
}
