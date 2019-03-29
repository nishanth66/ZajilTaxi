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
class users extends Model
{
    use SoftDeletes;

    public $table = 'users';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'name',
        'email',
        'password',
        'related',
        'repeat_password'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'related' => 'array',
        'repeat_password' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
    ];
//    protected $hidden = [
//        'password', 'remember_token',
//    ];


}
