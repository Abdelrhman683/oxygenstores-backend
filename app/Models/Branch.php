<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'vendor_id',
        'delegate_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'vendor_id' => 'integer',
        'delegate_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the seller/vendor that owns the branch.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'vendor_id');
    }

    /**
     * Get the metadata for the branch.
     */
    public function metas(): HasMany
    {
        return $this->hasMany(Branchmeta::class, 'branch_id');
    }

    /**
     * Get products associated with this branch.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'branch_product', 'branch_id', 'product_id');
    }
}
