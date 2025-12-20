<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'billiard_table_id',
        'start_time',
        'end_time',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billiardTable()
    {
        return $this->belongsTo(BilliardTable::class);
    }
}
