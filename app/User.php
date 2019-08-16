<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the stores for the user
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users');
    }

    /**
     * Get the providers for the user
     */
    public function providers()
    {
        return $this->hasMany(UserProvider::class);
    }

    /**
     * Check the case when have multiple rows.
     *
     * @return string
     */
    public function getShopifyAccessToken(): string
    {
        return $this->providers->where('provider', 'shopify')->first()->provider_token;
    }
}
