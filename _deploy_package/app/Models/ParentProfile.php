<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $table = 'parents';
    protected $primaryKey = 'parentID';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'street_address',
        'city',
        'barangay',
        'zipcode',
        'password_hash',
        'created_date',
        'last_login',
        'account_status',
        'userID',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'last_login' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function contributions()
    {
        return $this->hasMany(ProjectContribution::class, 'parentID', 'parentID');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'parentID', 'parentID');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student_relationships', 'parentID', 'studentID')
            ->withPivot(['relationship_type', 'is_primary_contact', 'created_date']);
    }
}
