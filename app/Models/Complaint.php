<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_id', 'complainant_name', 'college_id', 'user_id', 'category_id', 
        'category', 'complaint_text', 'title', 'priority', 'status', 'additional_data', 
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'additional_data' => 'array',
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function updates()
    {
        return $this->hasMany(ComplaintUpdate::class);
    }
}