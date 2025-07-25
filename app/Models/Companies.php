<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Companies extends Model
{
    //
    use HasFactory, Notifiable;

    protected $guarded = ['id'];
    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function invoices()
    {
        $this->hasMany(Invoices::class);
    }
}
