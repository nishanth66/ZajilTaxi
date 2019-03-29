<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class product1
 * @package App\Models
 * @version June 27, 2018, 6:04 am UTC
 *
 * @property string name
 * @property string image
 */
class product1 extends Model
{
    use SoftDeletes;

    public $table = 'product1s';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'name',
        'image'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'image' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'image' => 'required'
    ];

    
}
