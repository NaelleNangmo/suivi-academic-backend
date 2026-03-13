<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelActions;

/**
 * Class Ec
 *
 * @property string $code_ec
 * @property string $label_ec
 * @property string $desc_ec
 * @property int $nbh_ec
 * @property int $nbc_ec
 * @property string $code_ue
 * @property string|null $support_cours
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Ue $ue
 * @property Collection|Enseigne[] $enseignes
 * @property Collection|Programmation[] $programmations
 *
 * @package App\Models
 */
class Ec extends Model
{
    use HasFactory, LogsModelActions;
    
    protected $table = 'ec';
    protected $primaryKey = 'code_ec';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $casts = [
        'nbh_ec' => 'int',
        'nbc_ec' => 'int'
    ];

    protected $fillable = [
        'code_ec',
        'label_ec',
        'desc_ec',
        'nbh_ec',
        'nbc_ec',
        'code_ue',
        'support_cours'
    ];

    /**
     * Ajouter l'attribut support_cours_url à la réponse JSON
     */
    protected $appends = ['support_cours_url'];

    /**
     * Obtenir l'URL publique du support de cours
     * 
     * @return string|null
     */
    public function getSupportCoursUrlAttribute()
    {
        if ($this->support_cours) {
            return asset('storage/' . $this->support_cours);
        }
        return null;
    }

    /**
     * Vérifier si un support de cours existe
     * 
     * @return bool
     */
    public function hasSupportCours(): bool
    {
        return !empty($this->support_cours);
    }

    /**
     * Obtenir le nom du fichier du support de cours
     * 
     * @return string|null
     */
    public function getSupportCoursFileName()
    {
        if ($this->support_cours) {
            return basename($this->support_cours);
        }
        return null;
    }

    /**
     * Relations
     */
    public function ue()
    {
        return $this->belongsTo(Ue::class, 'code_ue', 'code_ue');
    }

    public function enseignes()
    {
        return $this->hasMany(Enseigne::class, 'code_ec', 'code_ec');
    }

    public function programmations()
    {
        return $this->hasMany(Programmation::class, 'code_ec', 'code_ec');
    }
}