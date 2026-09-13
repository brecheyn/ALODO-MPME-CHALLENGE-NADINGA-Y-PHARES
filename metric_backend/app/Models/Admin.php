<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens;   // donne à l'admin la capacité de créer/utiliser des tokens Sanctum

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];   // jamais renvoyé dans une réponse JSON
}
