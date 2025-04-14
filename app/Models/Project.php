<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'project_type',
        'address',
        'postal_code',
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

    //register media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
