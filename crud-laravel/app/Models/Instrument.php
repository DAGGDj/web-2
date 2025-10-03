<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    protected $table = 'products'; 
    protected $fillable = ['name', 'description', 'value', 'expiration_date', 'stock'];
}
