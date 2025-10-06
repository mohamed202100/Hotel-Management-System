<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'amount_due',
        'amount_paid',
        'tax_rate',
        'payment_status',
        'paid_at',
    ];


    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
