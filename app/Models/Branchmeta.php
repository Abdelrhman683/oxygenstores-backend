<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branchmeta extends Model
{
    protected $table = 'branchmeta';

    protected $primaryKey = 'meta_id';

    protected $fillable = [
        'branch_id',
        'meta_key',
        'meta_value',
    ];

    protected $casts = [
        'meta_id' => 'integer',
        'branch_id' => 'integer',
        'meta_key' => 'string',
        'meta_value' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the metadata.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
