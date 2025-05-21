<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Segment extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * The customers that belong to the Segment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'segment_customers', 'segment_id', 'customer_id');
    }
}
