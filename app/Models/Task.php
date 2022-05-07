<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * @var int
     */
    public mixed $created_by_id;
    protected $fillable = ['name', 'description', 'status_id', 'assigned_to_id', 'created_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
}
