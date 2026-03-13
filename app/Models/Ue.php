<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsModelActions;


/**
 * Class Ue
 *
 * @property string $code_ue
 * @property string $label_ue
 * @property string $desc_ue
 * @property int $code_niveau
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Niveau $niveau
 * @property Collection|Ec[] $ecs
 *
 * @package App\Models
 */
class Ue extends Model
{
    use HasFactory, LogsModelActions;
	protected $table = 'ue';
	protected $primaryKey = 'code_ue';
	public $incrementing = false;
    public $timestamps = true;

	protected $casts = [
		'code_niveau' => 'int'
	];

	protected $fillable = [
        'code_ue',
		'label_ue',
		'desc_ue',
		'code_niveau'
	];

	public function niveau()
	{
		return $this->belongsTo(Niveau::class, 'code_niveau');
	}

	public function ecs()
	{
		return $this->hasMany(Ec::class, 'code_ue');
	}
}
