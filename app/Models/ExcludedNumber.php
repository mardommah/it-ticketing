<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcludedNumber extends Model
{
    protected $fillable = ['number', 'note'];
}
