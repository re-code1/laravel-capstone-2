<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'grade_level', 'adviser_id'])]
class Section extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
        ];
    }

    /** Small helper: "Grade 7 Sampaguita". */
    public function fullName(): string
    {
        return "Grade {$this->grade_level} {$this->name}";
    }

    /** The teacher who advises this section (decides on its students' leave). */
    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** Every subject in this section = the course structure of its students. */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
