<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class users
 * @package App\Models
 * @version June 27, 2018, 6:45 am UTC
 *
 * @property string name
 * @property string user_email
 * @property string password
 * @property string repeat_password
 */
class feedback extends Model
{
    use SoftDeletes;

    public $table = 'feedback';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'type',
        'message',
        'user_id',
        'date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'string',
        'message' => 'string',
        'user_id' => 'string',
        'date' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];
//    protected $hidden = [
//        'password', 'remember_token',
//    ];


}
