<?php

namespace App\Http\Services;

use App\AjaxCrud;
use App\UserProfile;
use Illuminate\Support\Collection;
use Validator;

/**
 * Class UserService
 * @package App\Http\Services
 */
class UserService
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        return datatables()->of(AjaxCrud::with('userProfile')->get())
            ->addColumn('email', function ($data) {
                $email      =  $data->userProfile->email ;

                return $email;
            })
            ->addColumn('image', function ($data) {
                $image      =  $data->userProfile->image ;

                return $image;
            })
            ->addColumn('address1', function ($data) {
                $address1   =  $data->userProfile->address1 ;

                return $address1;
            })
            ->addColumn('address2', function ($data) {
                $address2   =  $data->userProfile->address2 ;

                return $address2;
            })
            ->addColumn('phone', function ($data) {
                $phone      =  $data->userProfile->phone ;

                return $phone;
            })
            ->addColumn('action', function ($data) {
                $button      = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm">Update</button>';
                $button     .= '&nbsp;&nbsp;';
                $button     .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @param Collection $collection
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Collection $collection)
    {
        $rules = [
            'first_name'    => 'required|max:50|alpha',
            'last_name'     => 'required|max:50|alpha',
            'email'         => 'required|email|unique:user_profile',
            'address1'      => 'required|max:150',
            'phone'         => 'required|digits:10',
            'image'         => 'required|image|max:2048',
        ];
        $error = Validator::make($collection->toArray(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $image      = $collection['image'];
        $new_name   = rand() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $new_name);

        $form_data  = [
            'first_name'    => $collection['first_name'],
            'last_name'     => $collection['last_name'],
        ];
        $userBasic      = AjaxCrud::create($form_data);
        $userId         = $userBasic->id;
        $prifile_data   = [
            'user_id'       => $userId,
            'email'         => $collection['email'],
            'address1'      => $collection['address1'],
            'address2'      => $collection['address2'],
            'phone'         => $collection['phone'],
            'image'         => $new_name
        ];

        UserProfile::create($prifile_data);

        return response()->json(['success' => 'User Added successfully.']);

    }

    /**
     * @param $id
     * @return AjaxCrud|AjaxCrud[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function edit($id)
    {
        return AjaxCrud::with('userProfile')->findOrFail($id);
    }

    /**
     * @param Collection $collection
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Collection $collection)
    {
        $image_name = $collection['hidden_image'];
        $image      = isset($collection['image']) ? $collection['image'] : '';
        $user = UserProfile::where(['user_id' => $collection['hidden_id']])->first();
        $userId     = $user->id;

        if ($image != '') {
            $rules = [
                'first_name'    => 'required|max:50|alpha',
                'last_name'     => 'required|max:50|alpha',
                'email'         => 'required|email|unique:user_profile,email,'.$userId,
                'address1'      => 'required|max:150',
                'phone'         => 'required|digits:10',
                'image'         => 'required|image|max:2048',
            ];
            $error = Validator::make($collection->toArray(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
            $image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $image_name);
        } else {
            $rules = [
                'first_name'    => 'required|max:50|alpha',
                'last_name'     => 'required|max:50|alpha',
                'email'         => 'required|email|unique:user_profile,email,'.$userId,
                'address1'      => 'required|max:150',
                'phone'         => 'required|digits:10',
            ];
            $error = Validator::make($collection->toArray(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }

        $form_data  = [
            'first_name'    => $collection['first_name'],
            'last_name'     => $collection['last_name'],
        ];
        $userBasic      = AjaxCrud::whereId($collection['hidden_id'])->update($form_data);
        $userId         = $collection['hidden_id'];
        $prifile_data   = [
            'email'         => $collection['email'],
            'address1'      => $collection['address1'],
            'address2'      => $collection['address2'],
            'phone'         => $collection['phone'],
            'image'         => $image_name
        ];

        UserProfile::where(['user_id' => $collection['hidden_id']])->update($prifile_data);

        return response()->json(['success' => 'User is successfully updated']);
    }

     /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data           = AjaxCrud::findOrFail($id)->delete();
        $dataProfile    = UserProfile::where(['user_id' => $id])->delete();

        return response()->json(['success' => 'User is successfully deleted']);
    }

}