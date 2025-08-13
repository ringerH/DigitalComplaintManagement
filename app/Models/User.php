<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name', 'phone', 'email', 'password', 'usertype', 'college_id'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function updates()
    {
    return $this->hasMany(Update::class, 'updated_by');
    }

    public function hasActiveComplaint()
    {
        return $this->complaints()
            ->whereIn('status', ['Pending', 'In Progress'])
            ->exists();
    }

    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    public function isStudent()
    {
        return $this->usertype === 'student';
    }

    public function isHospitalPatient()
    {
        return $this->usertype === 'hospital_patient';
    }
} 

