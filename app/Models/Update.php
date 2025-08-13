<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    protected $fillable = [
        'complaint_id', 'status', 'notes', 'updated_by'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}