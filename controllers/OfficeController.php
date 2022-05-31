<?php

namespace App\Modules\Office\Http\Controllers;

use App\Office;
use App\SettingOffices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Modules\Office\Model\OfficeGoal;

/**
 * Class OfficeController
 * @package App\Modules\Office\Http\Controllers
 */
class OfficeController extends Controller
{
    /**
     * OfficeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $year = date('Y');

        return view('office::index', compact('year'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (isset($request->year)) {
            $offices    = SettingOffices::select('settings_offices.*','office_goals.production_goal')
                            ->leftJoin('office_goals','office_goals.setting_offices_id','=','settings_offices.id')
                            ->where('office_goals.year','=',$request->year)
                            ->get();
            $view       = View::make('office::offices-part')->with(['offices' => $offices,'year' => $request->year]);
            $contents   = $view->render();

            return response()->json(['contents' =>  $contents]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = $this->validations($request);

        if ($errors['response'] == 'success') {
            $office                     = new SettingOffices();
            $office->office_name        = $request->get('office_name');
            $office->abbreviation       = $request->get('abbreviation');
            $office->address            = $request->get('address');
            $office->emphasys_id        = $request->get('emphasys_id');
            $office->created_by         = Auth::user()->id;
            $office->save();
            $production_goal = str_replace('$',"",$request->production_goal);
            $production_goal = trim(str_replace(',',"",$production_goal));
            $office->officegoal()->create([
                'production_goal'       => $production_goal,
                'year'                  => $request->get('year')
            ]);
        }

        return response()->json(['errors'   =>  $errors]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $errors = $this->validations($request);

        if ($errors['response'] == 'success') {
            $office                     = SettingOffices::find(\Crypt::decrypt($request->get('office_id')));
            $office->office_name        = $request->get('office_name');
            $office->abbreviation       = $request->get('abbreviation');
            $office->address            = $request->get('address');
            $office->emphasys_id        = $request->get('emphasys_id');
            $production_goal            = str_replace('$',"",$request->production_goal);
            $production_goal            = trim(str_replace(',',"",$production_goal));
            $office->save();
            $goal = $office->officegoal()->where('year',$request->year)->first();

            if (!$goal) {
                $office->officegoal()->create([
                    'production_goal'    => $production_goal,
                    'year'               => $request->year
                ]);
            } else {
                $goal->fill([
                    'production_goal'    => $production_goal,
                ]);
                $goal->update();
            }
        }

        return response()->json(['errors'   =>  $errors]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $office = SettingOffices::find($id)->delete();
            Session::flash('success', 'Office deleted successfully');
            $output['response'] = "success";
        } catch (Exception $e) {
            $output['response'] = "failure";
            Session::flash('danger', 'Found error in deleting Office. Please try again');
        }

        return response()->json($output);
    }

    /**
     * This function used to Validate requested fields data and return back with response.
     * @param Request $request
     * @return mixed
     */
    public function validations(Request $request){
        // Fields to Validate
        $rules = [
            'office_name'       =>'required',
            'abbreviation'      =>'required',
            'address'           =>'required',
            'production_goal'   =>'required',
            'emphasys_id'       =>'required',
        ];
        // Validation Messages
        $messages = [
            'office_name.required'      => 'Please enter Office name.',
            'abbreviation.required'     => 'Please enter Abbreviation.',
            'address.required'          => 'Please enter Address.',
            'production_goal.required'  => 'Please enter Production Goal.',
            'production_goal.numeric'   => 'Please enter Production Goal in numeric format only.',
            'emphasys_id.required'      => 'Please enter Emphasis ID.',
        ];
        // Validate the requested data and return error messages if the validation fails.
        $validator = \Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages(); // get error messages if validation fails
            
            foreach ($errors as $key => $error) {
                $msg[$key] = $error[0];
            }
            $output['response'] = "fail";
            $output['msgs']     = $msg;

            return $output;
        } else {
            $output['response'] = "success";

            return $output;
        }
    }
}
