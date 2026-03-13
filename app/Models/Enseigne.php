<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsModelActions;


/**
 * Class Enseigne
 *
 * @property string $code_pers
 * @property string $code_ec
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Ec $ec
 * @property Personnel $personnel
 *
 * @package App\Models
 */
class Enseigne extends Model
{
    use HasFactory, LogsModelActions;
	protected $table = 'enseigne';
	protected $primaryKey = ['code_pers', 'code_ec'];
	public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        "code_ec",
        "code_pers"
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
}
