<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'studentID';
    public $timestamps = false;

    protected $fillable = [
        'student_name',
        'grade_level',
        'section',
        'enrollment_status',
        'academic_year',
        'enrollment_date',
        'birth_date',
        'gender',
        'is_archived',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'birth_date' => 'date',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
        'is_archived' => 'boolean',
    ];

    /**
     * Scope a query to exclude archived students.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'parent_student_relationships', 'studentID', 'parentID')
            ->withPivot(['relationship_type', 'is_primary_contact', 'created_date']);
    }
}
