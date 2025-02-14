<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPasswordModel extends Model
{
    use HasFactory;

    protected $table = 'password_resets';
    protected $fillable = ['email', 'token'];
    public $timestamps = true;
}
