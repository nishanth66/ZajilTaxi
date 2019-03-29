<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="user_profile",
 *      required={"user_id", "first_name", "last_name", "driving_licence"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="driving_licence",
 *          description="driving_licence",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class fixed_routes extends Model
{
	use SoftDeletes;

	public $table = 'fixed_routes';


	protected $dates = ['deleted_at'];


	public $fillable = [
		'source_lat',
		'source_long',
		'destination_lat',
		'destination_long',
		'fixed_price',
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'source_lat' => 'string',
		'source_long' => 'string',
		'destination_lat' => 'string',
		'destination_long' => 'string',
		'fixed_price' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
}
