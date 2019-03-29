<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class importer
 * @package App\Models
 * @version June 29, 2018, 1:38 pm UTC
 *
 * @property string importer_name
 * @property string related
 * @property string destination
 * @property string source_sheet
 * @property string key_field
 * @property string feild_import
 * @property string field_type
 */
class app_users extends Model
{
    use SoftDeletes;

    public $table = 'app_users';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'mobile_number',
	    'otp',
        'lastRequested',
        'counter',
        'device_token',
        'otp_counter'
    ];
	protected $hidden = [
		'name'
	];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobile_number' => 'string',
        'otp' => 'string',
        'lastRequested' => 'string',
        'counter' => 'string',
        'otp_counter' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'mobile_number' => 'required'
    ];

    
}
