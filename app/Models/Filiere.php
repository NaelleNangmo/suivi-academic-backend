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
 * Class Filiere
 *
 * @property string $code_filiere
 * @property string $label_filiere
 * @property string $desc_filiere
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Niveau[] $niveaux
 *
 * @package App\Models
 */
class Filiere extends Model
{
    use HasFactory, LogsModelActions;
	protected $table = 'filiere';
	protected $primaryKey = 'code_filiere';
	public $incrementing = false;
    public $timestamps = true;

	protected $fillable = [
        'code_filiere',
		'label_filiere',
		'desc_filiere'
	];

	public function niveaux()
	{
		return $this->hasMany(Niveau::class, 'code_filiere');
	}
}
