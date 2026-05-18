<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = ['nama', 'no_hp', 'alamat'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
