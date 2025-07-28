<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class InvoiceItem extends Model
{
//
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    public function Invoice()
    {
        return  $this->belongsTo(Invoice::class);
    }
}
