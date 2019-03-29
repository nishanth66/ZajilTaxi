<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class permission
 * @package App\Models
 * @version June 28, 2018, 7:20 am UTC
 *
 * @property string related
 */
class permission extends Model
{
    use SoftDeletes;

    public $table = 'permissions';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'related'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'related' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'related' => 'required'
    ];

    
}
