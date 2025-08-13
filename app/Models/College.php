<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $fillable = ['name'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'college_id');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}