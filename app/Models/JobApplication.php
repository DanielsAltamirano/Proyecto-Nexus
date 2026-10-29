<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = ['vacancy_id', 'user_id', 'answers', 'cv_path', 'status'];

    protected $casts = [
        'answers' => 'array',
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
