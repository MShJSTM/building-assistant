<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'project_type',
        'address',
        'postal_plate',
        'land_area',
        'building_area',
        'structure_type',
        'start_date',
        'end_date',
        'permit_start_date',
        'permit_end_date'
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'permit_start_date' => 'date',
        'permit_end_date' => 'date',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
    
}
