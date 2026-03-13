<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsModelActions;


class Programmation extends Model
{
    use HasFactory, LogsModelActions;
	protected $table = 'programmation';
	protected $primaryKey = ['code_ec', 'num_salle', 'code_pers'];
	public $incrementing = false;
    public $timestamps = true;

	protected $casts = [
		'date' => 'datetime',
		'date-debut' => 'datetime',
		'date_fin' => 'datetime',
		'nbre_heure' => 'int'
	];

	protected $fillable = [
		'code_ec',
		'num_salle',
		'code_pers',
		'date',
		'date-debut',
		'date_fin',
		'nbre_heure',
		'statut'
	];

	// Indique à Laravel de ne pas utiliser 'id'
	protected function setKeysForSaveQuery($query)
	{
		$keys = $this->getKeyName();
		if(!is_array($keys)){
			return parent::setKeysForSaveQuery($query);
		}

		foreach($keys as $keyName){
			$query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
		}

		return $query;
	}

	protected function getKeyForSaveQuery($keyName = null)
	{
		if(is_null($keyName)){
			$keyName = $this->getKeyName();
		}

		if (isset($this->original[$keyName])) {
			return $this->original[$keyName];
		}

		return $this->getAttribute($keyName);
	}

	public function ec()
	{
		return $this->belongsTo(Ec::class, 'code_ec');
	}

	public function personnel()
	{
		return $this->belongsTo(Personnel::class, 'code_pers');
	}

	public function salle()
	{
		return $this->belongsTo(Salle::class, 'num_salle');
	}
}
