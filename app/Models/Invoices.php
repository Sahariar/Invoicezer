<?php

namespace App\Models;

use Faker\Provider\ar_EG\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Invoices extends Model
{
    //
    use HasFactory, Notifiable;

    protected $guarded = ['id'];
    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function client()
    {
        $this->belongsTo(Client::class);
    }
    public function company()
    {
        $this->belongsTo(Companies::class);
    }
    public function InvoiceItems()
    {
        $this->hasMany(InvoiceItem::class);
    }
}
