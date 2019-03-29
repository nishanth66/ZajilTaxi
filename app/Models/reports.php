<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class reports
 * @package App\Models
 * @version July 3, 2018, 5:04 am UTC
 *
 * @property string report_name
 * @property string related
 * @property string sql_string
 * @property string titles
 * @property string orientation
 * @property string next_query
 * @property string graphic_type
 * @property string x_axis
 * @property string y_axis
 */
class reports extends Model
{
    use SoftDeletes;

    public $table = 'reports';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'report_name',
        'related',
        'sql_string',
        'titles',
        'orientation',
        'next_query',
        'graphic_type',
        'x_axis',
        'y_axis'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'report_name' => 'string',
        'related' => 'string',
        'sql_string' => 'string',
        'titles' => 'string',
        'orientation' => 'string',
        'next_query' => 'string',
        'graphic_type' => 'string',
        'x_axis' => 'string',
        'y_axis' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'report_name' => 'required',
        'related' => 'required',
        'sql_string' => 'required',
        'titles' => 'required',
        'orientation' => 'required',
        'next_query' => 'required',
        'graphic_type' => 'required',
        'x_axis' => 'required',
        'y_axis' => 'required'
    ];

    
}
