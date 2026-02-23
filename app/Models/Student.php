<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lrn',
        'last_name',
        'first_name',
        'middle_name',
        'dob',
        'sex',
        'civil_status',
        'address',
        'academic_year',
        'grade_level',
        'section',
        'student_type',
        'date_enrolled',
        'status',
        'guardian_name',
        'relationship',
        'contact',
        'email',
    ];

    protected $casts = [
        'dob'           => 'date',
        'date_enrolled' => 'date',
    ];

    // ── Scopes ──────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByGrade($query, string $grade)
    {
        return $query->where('grade_level', $grade);
    }

    public function scopeFilter($query, $request)
    {
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }
        if ($request->filled('from')) {
            $query->whereDate('date_enrolled', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date_enrolled', '<=', $request->to);
        }
        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }
        return $query;
    }

    // ── Relationships ────────────────────────────────
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name[0]}." : '';
        return "{$this->last_name}, {$this->first_name}{$middle}";
    }

    public function getAgeAttribute(): int
    {
        return $this->dob?->age ?? 0;
    }
}
