<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'notes',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function scopeStatus($query, ?string $status)
    {
        if ($status && in_array($status, ['pending', 'done'], true)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $term = trim($term);

            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}
