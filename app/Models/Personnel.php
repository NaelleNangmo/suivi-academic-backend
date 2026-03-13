<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsModelActions;


class Personnel extends Authenticatable
{
    use HasApiTokens, HasFactory, LogsModelActions;

    protected $table = 'personnel';
    protected $primaryKey = 'code_pers';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'code_pers',
        'nom_pers',
        'prenom_pers',
        'sexe_pers',
        'phone_pers',
        'login_pers',
        'pwd_pers',
        'type_pers'
    ];

    // Important : Sanctum doit connaître la colonne mot de passe !
    public function getAuthPassword()
    {
        return $this->pwd_pers;
    }

    public function enseignes()
    {
        return $this->hasMany(Enseigne::class, 'code_pers');
    }

    public function programmations()
    {
        return $this->hasMany(Programmation::class, 'code_pers');
    }
}
