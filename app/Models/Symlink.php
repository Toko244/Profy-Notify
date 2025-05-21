<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Symlink extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'original', 'symlink', 'usage_count'];
}
