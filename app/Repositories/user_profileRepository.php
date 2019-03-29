<?php

namespace App\Repositories;

use App\Models\user_profile;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class user_profileRepository
 * @package App\Repositories
 * @version October 1, 2018, 8:40 am UTC
 *
 * @method user_profile findWithoutFail($id, $columns = ['*'])
 * @method user_profile find($id, $columns = ['*'])
 * @method user_profile first($columns = ['*'])
*/
class user_profileRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'first_name',
        'last_name',
        'driving_licence',
        'image'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return user_profile::class;
    }
}
