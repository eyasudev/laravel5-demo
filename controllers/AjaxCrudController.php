<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\UserService;

/**
 * Class AjaxCrudController
 * @package App\Http\Controllers
 */
class AjaxCrudController extends Controller
{
    /**
     * Show listing of users records
     *
     * @param UserService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \Exception
     *
     */
    public function index(UserService $service)
    {
        if (request()->ajax()) {
            return $service->index();
        }

        return view('ajax_index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param UserService $service
     * @return \Exception|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request , UserService $service)
    {
        try{
            $collection = collect($request->all());

            return $service->store($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @param UserService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id, UserService $service)
    {
        if (request()->ajax()) {
            $data = $service->edit($id);

            return response()->json(['data' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param UserService $service
     * @return \Exception|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, UserService $service)
    {
        try{
            $collection = collect($request->all());

            return $service->update($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * @param $id
     * @param UserService $service
     * @return \Exception
     */
    public function destroy($id, UserService $service)
    {
        try{
           return  $service->destroy($id);
        } catch (\Exception $e) {
            return $e;
        }
    }
}

