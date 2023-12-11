<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\LogoCast;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'path_logo',
        'header',
        'footer',
        'third_id'
    ];

    protected $casts = [
        'path_logo' => LogoCast::class,
    ];

    /**__________________________________________
     *                RELATIONSHIP
     * ___________________________________________
     */
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
}