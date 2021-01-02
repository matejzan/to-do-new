<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'taskname',
        'taskdescription',
        'taskduedate',
        'importance',
        'status',
        'taskcolor',
        'user_id',
        'created_at' => 'datetime',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $table = 'tasks';

    //public $timestamps = false;

}
