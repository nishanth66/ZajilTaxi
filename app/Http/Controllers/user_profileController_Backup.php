<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createuser_profileRequest;
use App\Http\Requests\Updateuser_profileRequest;
use App\Models\user_profile;
use App\Repositories\user_profileRepository;
use App\Models\user_profile_status;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class user_profileController extends AppBaseController
{
    /** @var  user_profileRepository */
    private $userProfileRepository;


    public function __construct(user_profileRepository $userProfileRepo)
    {
        $this->middleware('auth');
        $this->userProfileRepository = $userProfileRepo;
    }

    /**
     * Display a listing of the user_profile.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userProfileRepository->pushCriteria(new RequestCriteria($request));
        $userProfiles = $this->userProfileRepository->all();

        return view('user_profiles.index')->with('userProfiles', $userProfiles);
    }

    /**
     * Show the form for creating a new user_profile.
     *
     * @return Response
     */
    public function create()
    {
        if (Auth::user()->status == 'admin') {
            return view('user_profiles.create');
        }
    }

    /**
     * Store a newly created user_profile in storage.
     *
     * @param Createuser_profileRequest $request
     *
     * @return Response
     */
    public function store(Createuser_profileRequest $request)
    {
        $input = $request->all();

        $userProfile = $this->userProfileRepository->create($input);

        Flash::success('User Profile saved successfully.');

        return redirect(route('userProfiles.index'));
    }

    /**
     * Display the specified user_profile.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $userProfile = $this->userProfileRepository->findWithoutFail($id);

        if (empty($userProfile)) {
            Flash::error('User Profile not found');

            return redirect(route('userProfiles.index'));
        }

        return view('user_profiles.show')->with('userProfile', $userProfile);
    }

    /**
     * Show the form for editing the specified user_profile.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userProfile = $this->userProfileRepository->findWithoutFail($id);

        if (empty($userProfile)) {
            Flash::error('User Profile not found');

            return redirect(route('userProfiles.index'));
        }

        return view('user_profiles.edit')->with('userProfile', $userProfile);
    }

    /**
     * Update the specified user_profile in storage.
     *
     * @param  int              $id
     * @param Updateuser_profileRequest $request
     *
     * @return Response
     */
    public function update($id, Updateuser_profileRequest $request)
    {
        $userProfile = $this->userProfileRepository->findWithoutFail($id);

        if (empty($userProfile)) {
            Flash::error('User Profile not found');

            return redirect(route('userProfiles.index'));
        }

        $userProfile = $this->userProfileRepository->update($request->all(), $id);

        Flash::success('User Profile updated successfully.');

        return redirect(route('userProfiles.index'));
    }

    /**
     * Remove the specified user_profile from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $userProfile = $this->userProfileRepository->findWithoutFail($id);

        if (empty($userProfile)) {
            Flash::error('User Profile not found');

            return redirect(route('userProfiles.index'));
        }

        $this->userProfileRepository->delete($id);

        Flash::success('User Profile deleted successfully.');

        return redirect(route('userProfiles.index'));
    }
    public function showColumn()
    {
        if (Auth::user()->status == 'admin') {
            return view('user_profiles/index');
        }
        return redirect('/home');
    }
    public function addColumn()
    {
        if (Auth::user()->status == 'admin') {
            return view('user_profiles/column');
        }
        return redirect('/home');
    }
    public function addCustomerColumn()
    {
        if (Auth::user()->status == 'admin') {
            return view('user_profiles/column2');
        }
        return redirect('/home');
    }
    public function newColumn(Request $request)
    {
        $last =DB::table('driver_profile_status')->orderby('position', 'desc')->first();
        if (isset($last->position)) {
            $last_pos = $last->position;
        }
        else
        {
            $last_pos=1;
        }
        $current_pos = (int)$request->pos;
        if (DB::table('driver_profile_status')->where('position',$request->pos)->exists())
        {
//            $pos = $request->pos;
            for($i=$current_pos;$i<=$last_pos;$i++)
            {
                $newPos=(int)$i+1;
                $input5['position']=$newPos;
                $value=DB::table('driver_profile_status')->where('position',$i)->orderby('position','desc')->first();
                $value_id = $value->id;
                DB::table('driver_profile_status')->whereId($value_id)->update($input5);
            }
        }
        if (Auth::user()->status == 'admin') {
            $name1=str_replace(' ','_',$request->column_en);
            $name2=str_replace(' ','_',$request->column_ar);
            if ($name1 == $name2)
            {
                return Redirect()->back()->withError( 'English and Arabic column name can not be same' );
            }
            $EngName=str_replace('-','_',$name1);
            $ArName =str_replace('-','_',$name2);
            if (DB::table('driver_profile_status')->where('column_name_en', '' . $request->column_en . '')->orWhere('column_name_ar', '' . $request->column_ar . '')->exists()) {
                if (Schema::hasColumn('driver_profile', $EngName))
                {
                    return Redirect()->back()->withError( 'Column is already exist. Please Try Again' );
                }
            }
            elseif (Schema::hasColumn('driver_profile', $EngName))
            {
                $input['column_name_en'] = $request->column_en;
                $input['column_name_ar'] = $request->column_ar;
                $input['type_en'] = $request->type;
                $input['type_ar'] = $request->type;
                $input['position'] = $request->pos;
                DB::table('driver_profile_status')->insert($input);
                return redirect('show/columns');
            }
            else
            {
                Schema::table('driver_profile', function ($table) use ($EngName)
                {
                    $table->text('' . $EngName . '')->nullable();
//                    $table->text('' . $ArName . '')->nullable();
                });
                Artisan::call("migrate");
                $input['column_name_en'] = $request->column_en;
                $input['column_name_ar'] = $request->column_ar;
                $input['type_en'] = $request->type;
                $input['type_ar'] = $request->type;
                $input['position'] = $request->pos;
                DB::table('driver_profile_status')->insert($input);

            }
        }
        return redirect('show/columns');

    }
    public function newCustomerColumn(Request $request)
    {
        $last =DB::table('customer_profile_status')->orderby('position', 'desc')->first();
        if (isset($last->position)) {
            $last_pos = $last->position;
        }
        else
        {
            $last_pos=1;
        }
        $current_pos = (int)$request->pos;


        if (DB::table('customer_profile_status')->where('position',$request->pos)->exists())
        {
//            $pos = $request->pos;
            for($i=$current_pos;$i<=$last_pos;$i++)
            {
                $newPos=(int)$i+1;
                $input5['position']=$newPos;
                $value=DB::table('customer_profile_status')->where('position',$i)->orderby('position','desc')->first();
                $value_id = $value->id;
                DB::table('customer_profile_status')->whereId($value_id)->update($input5);
            }
        }

        if (Auth::user()->status == 'admin') {
            $name1 = str_replace(' ', '_', $request->column_en);
            $name2 = str_replace(' ', '_', $request->column_ar);
            if ($name1 == $name2)
            {
                return Redirect()->back()->withError( 'English and Arabic column name can not be same' );
            }
            $name_en = str_replace('-', '_', $name1);
            $name_ar = str_replace('-', '_', $name2);
            if (DB::table('customer_profile_status')->where('column_name_en', '' . $request->column_en . '')->orWhere('column_name_ar', '' . $request->column_ar . '')->exists()) {
                if (Schema::hasColumn('customer_profile', $name_en))  //check whether users table has email column
                {
                    return Redirect()->back()->withError( 'Column is already exist. Please Try Again' );
                }
            } elseif (Schema::hasColumn('customer_profile', $name_en))  //check whether users table has email column
            {
                $input['column_name_en'] = $request->column_en;
                $input['column_name_ar'] = $request->column_ar;
                $input['type_en'] = $request->type;
                $input['type_ar'] = $request->type;
                $input['position'] = $request->pos;
                DB::table('customer_profile_status')->insert($input);
                return redirect('show/customer/columns');
            } else {

                Schema::table('customer_profile', function ($table) use ($name_en) {
                    $table->text('' . $name_en . '')->nullable();
//                    $table->text('' . $name_ar . '')->nullable();
                });
                Artisan::call("migrate");
                $input['column_name_en'] = $request->column_en;
                $input['column_name_ar'] = $request->column_ar;
                $input['type_en'] = $request->type;
                $input['type_ar'] = $request->type;
                $input['position'] = $request->pos;
                DB::table('customer_profile_status')->insert($input);
            }
        }
        return redirect('show/customer/columns');
    }
    public function changeStatus($id)
    {
        $current=user_profile_status::whereId($id)->first();
        if ($current->status == 'enabled')
        {
            $input['status'] = 'disabled';
            if (Auth::user()->status == 'admin') {
                user_profile_status::whereId($id)->update($input);
            }
            return "Success";
        }
        else if ($current->status == 'disabled')
        {
            $input['status'] = 'enabled';
            if (Auth::user()->status == 'admin') {
                user_profile_status::whereId($id)->update($input);
            }
            return "Success";
        }
        else
            return "failed";

    }
    public function changeCustomerStatus($id)
    {
        $current=DB::table('customer_profile_status')->whereId($id)->first();
        if ($current->status == 'enabled')
        {
            $input['status'] = 'disabled';
            if (Auth::user()->status == 'admin') {
                DB::table('customer_profile_status')->whereId($id)->update($input);
            }
            return "Success";
        }
        else if ($current->status == 'disabled')
        {
            $input['status'] = 'enabled';
            if (Auth::user()->status == 'admin') {
                DB::table('customer_profile_status')->whereId($id)->update($input);
            }
            return "Success";
        }
        else
            return "failed";

    }
    public function editColumn(Request $request)
    {
        $last =\Illuminate\Support\Facades\DB::table('driver_profile_status')->orderby('position','desc')->first();
        $last_pos = $last->position;
        $current_pos = (int)$request->pos;
        $prev_pos = (int)$request->prev_pos;
        if (DB::table('driver_profile_status')->where('position',$current_pos)->exists()) {
            if ($current_pos < $prev_pos) {
//                return "aaaaa";
                $prev_pos = (int)$request->prev_pos - 1;
                for ($i = $prev_pos; $i >= $current_pos; $i--) {
                    $newPos = (int)$i + 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('driver_profile_status')->where('position', $i)->first();
                    $value_id = $value->id;
                    DB::table('driver_profile_status')->whereId($value_id)->update($input5);
                }
            } elseif ($current_pos > $prev_pos) {
//                return "bbbb";
                $prev_pos = (int)$request->prev_pos + 1;
                for ($i = $prev_pos; $i<= $current_pos ; $i++) {
                    $newPos = (int)$i - 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('driver_profile_status')->where('position', $i)->first();
                    $value_id = $value->id;
//                    return $value_id;
                    DB::table('driver_profile_status')->whereId($value_id)->update($input5);
                }
            }
        }
        $id = $request->id;
        $prev_en1 = str_replace(' ','_',$request->prev_en);
        $prev_en = str_replace('-','_',$prev_en1);
        $prev_ar1 = str_replace(' ','_',$request->prev_ar);
        $prev_ar = str_replace('-','_',$prev_ar1);
        $column1 = str_replace(' ','_',$request->column_name_en);
        $column2 = str_replace(' ','_',$request->column_name_ar);
        $column_en = str_replace('-','_',$column1);
        $column_ar = str_replace('-','_',$column2);

        if ($request->column_name_en == $request->column_name_ar) {
            return Redirect()->back()->withError('English and Arabic column name can not be same');
        }

        $input['column_name_en'] = $request->column_name_en;
        $input['column_name_ar'] = $request->column_name_ar;
        $input['type_en'] = $request->type;
        $input['type_ar'] = $request->type;
        $input['position'] = $request->pos;
        if (Auth::user()->status == 'admin') {
            DB::table('driver_profile_status')->whereId($id)->update($input);
            Schema::table('driver_profile', function ($table) use ($prev_en, $column_en) {
                $table->renameColumn($prev_en, $column_en);
//                $table->renameColumn($prev_ar, $column_ar);
            });

        }
        return redirect('show/columns');
    }
    public function editCustomerColumn(Request $request)
    {
        $last =\Illuminate\Support\Facades\DB::table('customer_profile_status')->orderby('position','desc')->first();
        $last_pos = $last->position;
        $current_pos = (int)$request->pos;
        $prev_pos = (int)$request->prev_pos;
        if (DB::table('customer_profile_status')->where('position',$current_pos)->exists()) {
            if ($current_pos < $prev_pos) {
//                return "aaaaa";
                $prev_pos = (int)$request->prev_pos - 1;
                for ($i = $prev_pos; $i >= $current_pos; $i--) {
                    $newPos = (int)$i + 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('customer_profile_status')->where('position', $i)->first();
                    $value_id = $value->id;
                    DB::table('customer_profile_status')->whereId($value_id)->update($input5);
                }
            } elseif ($current_pos > $prev_pos) {
//                return "bbbb";
                $prev_pos = (int)$request->prev_pos + 1;
                for ($i = $prev_pos; $i<= $current_pos ; $i++) {
                    $newPos = (int)$i - 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('customer_profile_status')->where('position', $i)->first();
                    $value_id = $value->id;
//                    return $value_id;
                    DB::table('customer_profile_status')->whereId($value_id)->update($input5);
                }
            }
        }

        $prev_en1 = str_replace(' ','_',$request->prev_en);
        $prev_en = str_replace('-','_',$prev_en1);
        $prev_ar1 = str_replace(' ','_',$request->prev_ar);
        $prev_ar = str_replace('-','_',$prev_ar1);
        $column1 = str_replace(' ','_',$request->column_name_en);
        $column2 = str_replace(' ','_',$request->column_name_ar);
        $column_en = str_replace('-','_',$column1);
        $column_ar = str_replace('-','_',$column2);
        if ($request->column_name_en == $request->column_name_ar)
        {
            return Redirect()->back()->withError( 'English and Arabic column name can not be same' );
        }
        $id = $request->id;
        $input['column_name_en'] = $request->column_name_en;
        $input['column_name_ar'] = $request->column_name_ar;
        $input['type_en'] = $request->type;
        $input['type_ar'] = $request->type;
        $input['position'] = $request->pos;
        if (Auth::user()->status == 'admin') {
            DB::table('customer_profile_status')->whereId($id)->update($input);
            Schema::table('customer_profile', function ($table) use ($prev_en, $column_en) {
                $table->renameColumn($prev_en, $column_en);
//                    $table->renameColumn($prev_ar, $column_ar);
            });
            // Closures include ->first(), ->get(), ->pluck(), etc.
        }
        return redirect('show/customer/columns');
    }
    public function showCustomer()
    {
        return view('user_profiles/index2');
    }
    public function deleteDColumn($id)
    {
        $last =DB::table('driver_profile_status')->orderby('position','desc')->first();
        $last_pos = (int)$last->position;
        $column = DB::table('driver_profile_status')->whereId($id)->first();
        $e = $column->column_name_en;
        $a = $column->column_name_ar;
        $p = (int)$column->position + 1;

        for ($i=$p;$i<=$last_pos;$i++)
        {
            $newPos=(int)$i-1;
            $input5['position']=$newPos;
            $value=DB::table('driver_profile_status')->where('position',$i)->first();
            $value_id = $value->id;
            DB::table('driver_profile_status')->whereId($value_id)->update($input5);
        }


        $english = str_replace(' ','_',$e);
        $col_en = str_replace('-','_',$english);
        $arabic = str_replace(' ','_',$a);
        $col_ar = str_replace('-','_',$arabic);



        if (Schema::hasColumn('driver_profile', $col_en)) {
            Schema::table('driver_profile', function ($table) use ($col_en) {
                $table->dropColumn($col_en);
            });
        }
        else
        {
            return redirect('show/columns');
        }

        DB::table('driver_profile_status')->whereId($id)->delete();
        return redirect('show/columns');

    }
    public function deleteCColumn($id)
    {

        $last =DB::table('customer_profile_status')->orderby('position','desc')->first();
        $last_pos = (int)$last->position;
        $column = DB::table('customer_profile_status')->whereId($id)->first();
        $e = $column->column_name_en;
        $a = $column->column_name_ar;
        $p = (int)$column->position + 1;

        for ($i=$p;$i<=$last_pos;$i++)
        {
            $newPos=(int)$i-1;
            $input5['position']=$newPos;
            $value=DB::table('customer_profile_status')->where('position',$i)->first();
            $value_id = $value->id;
            DB::table('customer_profile_status')->whereId($value_id)->update($input5);
        }


        $english = str_replace(' ','_',$e);
        $col_en = str_replace('-','_',$english);
//        return $col_en;
        $arabic = str_replace(' ','_',$a);
        $col_ar = str_replace('-','_',$arabic);



        if (Schema::hasColumn('customer_profile', $col_en)) {
            Schema::table('customer_profile', function ($table) use ($col_en) {
                $table->dropColumn($col_en);
            });
        }
        else
        {
            return redirect('show/customer/columns');
        }
        DB::table('customer_profile_status')->whereId($id)->delete();
        return redirect('show/customer/columns');
    }
    public function changeDriverStatus($id,$status)
    {
        $current = DB::table('driver_profile')->whereId($id)->first();
        $update_input['status'] = $status;
        $st=DB::table('driver_profile')->whereId($id)->update($update_input);
//        return $st;
    }
}
