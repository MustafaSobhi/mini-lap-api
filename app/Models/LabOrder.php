<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'test_code',
        'priority',
        'status',
        'scheduled_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function scopeStatus($q, $status)
    {
        if ($status) $q->where('status', $status);
    }

    public function scopeDateRange($q, $from, $to)
    {
        if ($from && $to)    return $q->whereBetween('scheduled_at', [$from, $to]);
        if ($from)           return $q->where('scheduled_at', '>=', $from);
        if ($to)             return $q->where('scheduled_at', '<=', $to);
        return $q;
    }

    public function scopeSearch($q, $term)
    {
        if ($term) {
            $q->where(function ($sub) use ($term) {
                $sub->where('patient_name', 'like', "%{$term}%")
                    ->orWhere('test_code', 'like', "%{$term}%");
            });
        }
    }
}
