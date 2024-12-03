<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'start_date',
        'end_date',
        'subscription_id',
    ];


    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
