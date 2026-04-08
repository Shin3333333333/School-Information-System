<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    /**
     * Table associated with this model
     */
    protected $table = 'academic_years';

    /**
     * Attributes that are mass assignable
     */
    protected $fillable = [
        'year_label',
        'is_active',
    ];

    /**
     * Attributes that should be cast to native types
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================================================
    // STATIC METHODS - Get data
    // ========================================================================

    /**
     * Get the currently active academic year
     * 
     * @return AcademicYear|null
     */
    public static function active()
    {
        return static::where('is_active', 1)->first();
    }

    /**
     * Get all academic years ordered by ID descending
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllYears()
    {
        return static::orderBy('id', 'desc')->get();
    }

    /**
     * Check if a year label already exists
     * 
     * @param string $yearLabel
     * @return bool
     */
    public static function yearExists(string $yearLabel)
    {
        return static::where('year_label', $yearLabel)->exists();
    }

    /**
     * Get a year by its ID
     * 
     * @param int $id
     * @return AcademicYear|null
     */
    public static function getYearById(int $id)
    {
        return static::find($id);
    }

    /**
     * Get a year by its label
     * 
     * @param string $yearLabel
     * @return AcademicYear|null
     */
    public static function getYearByLabel(string $yearLabel)
    {
        return static::where('year_label', $yearLabel)->first();
    }

    // ========================================================================
    // INSTANCE METHODS - Instance operations
    // ========================================================================

    /**
     * Check if this year is currently active
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active === 1 || $this->is_active === true;
    }

    /**
     * Set this academic year as active
     * Deactivates all other years
     * 
     * @return bool
     */
    public function setAsActive()
    {
        // Deactivate all years
        AcademicYear::query()->update(['is_active' => 0, 'updated_at' => now()]);

        // Activate this year
        return $this->update(['is_active' => 1]);
    }

    /**
     * Deactivate this academic year
     * 
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['is_active' => 0]);
    }

    /**
     * Get the next academic year (chronologically)
     * Returns null if this is the last year
     * 
     * @return AcademicYear|null
     */
    public function nextYear()
    {
        return AcademicYear::where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Get the previous academic year (chronologically)
     * Returns null if this is the first year
     * 
     * @return AcademicYear|null
     */
    public function previousYear()
    {
        return AcademicYear::where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Check if this year can be deleted
     * A year can be deleted if:
     * - It's not the active year
     * 
     * @return bool
     */
    public function canBeDeleted()
    {
        return !$this->isActive();
    }

    /**
     * Get human-readable status
     * 
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // ========================================================================
    // RELATIONSHIPS - Define model relationships
    // ========================================================================

    /**
     * Uncomment and use these relationships when you have the related models
     * 
     * A year can have many sections
     * public function sections()
     * {
     *     return $this->hasMany(Section::class, 'academic_year_id');
     * }
     * 
     * A year can have many grades
     * public function grades()
     * {
     *     return $this->hasMany(Grade::class, 'academic_year_id');
     * }
     * 
     * A year can have many students enrolled
     * public function students()
     * {
     *     return $this->hasMany(Student::class, 'academic_year_id');
     * }
     * 
     * A year can have many enrollments
     * public function enrollments()
     * {
     *     return $this->hasMany(Enrollment::class, 'academic_year_id');
     * }
     */

    // ========================================================================
    // SCOPES - Query builders
    // ========================================================================

    /**
     * Scope to get only active years
     * 
     * Usage: AcademicYear::active()->get()
     * or use the static active() method above instead
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope to get only inactive years
     * 
     * Usage: AcademicYear::isInactive()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsInactive($query)
    {
        return $query->where('is_active', 0);
    }

    /**
     * Scope to order by creation date
     * 
     * Usage: AcademicYear::orderByNewest()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order by oldest first
     * 
     * Usage: AcademicYear::orderByOldest()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    // ========================================================================
    // ACCESSORS & MUTATORS - Attribute manipulation
    // ========================================================================

    /**
     * Get formatted year label
     * Example: Returns "2024 - 2025" or "2024–2025" consistently
     * 
     * @return string
     */
    public function getFormattedLabel()
    {
        // Normalize the label (ensure consistent formatting)
        return str_replace('-', '–', $this->year_label);
    }

    /**
     * Get status color for UI display
     * 
     * @return string
     */
    public function getStatusColor()
    {
        return $this->is_active ? '#10b981' : '#ef4444'; // green or red
    }

    /**
     * Get status badge class for UI display
     * 
     * @return string
     */
    public function getStatusBadgeClass()
    {
        return $this->is_active ? 'badge-success' : 'badge-danger';
    }

    // ========================================================================
    // EVENTS - Model lifecycle hooks
    // ========================================================================

    /**
     * Model boot method - set up global scopes and observers if needed
     */
    protected static function boot()
    {
        parent::boot();
    }

    // ========================================================================
    // UTILITY METHODS - Helper functions
    // ========================================================================

    /**
     * Get all years as key-value pairs for select/dropdown
     * Useful for HTML <select> elements
     * 
     * @return array Format: [id => 'year_label']
     */
    public static function getForSelect()
    {
        return static::orderBy('id', 'desc')->pluck('year_label', 'id')->toArray();
    }

    /**
     * Get count of total academic years
     * 
     * @return int
     */
    public static function getTotalCount()
    {
        return static::count();
    }

    /**
     * Get count of active academic years (should always be 0 or 1)
     * 
     * @return int
     */
    public static function getActiveCount()
    {
        return static::where('is_active', 1)->count();
    }

    /**
     * Export all years as array
     * 
     * @return array
     */
    public function toExportArray()
    {
        return [
            'ID' => $this->id,
            'Year Label' => $this->year_label,
            'Status' => $this->getStatusAttribute(),
            'Created' => $this->created_at->format('Y-m-d H:i:s'),
            'Updated' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Convert model to array with formatted data
     * 
     * @return array
     */
    public function toFormattedArray()
    {
        return [
            'id' => $this->id,
            'year_label' => $this->getFormattedLabel(),
            'is_active' => $this->is_active,
            'status' => $this->getStatusAttribute(),
            'created_at' => $this->created_at->toDateTimeString(),
            'created_at_formatted' => $this->created_at->format('M d, Y'),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Get JSON representation
     * 
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toFormattedArray(), $options);
    }
}