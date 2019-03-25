<?php

namespace App\Http\Controllers;

use App\Category;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class ApiCategoryController extends Controller
{

    public function index()
    {
        $categorys = Category::get();
        if(count($categorys) > 0) {
            return response()->json(['code' => 200, 'massage' => 'success','data' => $categorys]);
        }
        return response()->json(['code' => 200,'massage' => 'Data Not found','data' => []]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|unique:categories',
        ],[
            'name.required'         => 'The category field is required',
            'name.unique'           => 'The category name has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'       => 422,
                'massage'    => apiValidateError($validator->errors()),
                'data'       => null
            ]);
        }

        try{
            $result = Category::create($request->all());
            return response()->json(['code' => 200,'massage' => 'Category successfully saved.','data' => $result]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'something error found, please try again!','data' => null]);
        }
    }

    public function edit($id)
    {
        $editModeData = Category::FindOrFail($id);
        if( $editModeData ){
            return response()->json(['code' => 200,'massage' => 'success','data' => $editModeData]);
        }
        return response()->json(['code' => 200,'massage' => 'Data not found','data' => null]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'                 =>'required|unique:categories,name,'.$id.',id',
        ],[
            'name.required'         => 'The category field is required',
            'name.unique'           => 'The category name has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'       => 422,
                'massage'    => apiValidateError($validator->errors()),
                'data'       => null
            ]);
        }

        $data = Category::FindOrFail($id);
        try{
            $data->update($request->all());
            return response()->json(['code' => 200,'massage' => 'Category successfully updated.','data' => $data]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'something error found,please try again','data' => null]);
        }
    }

    public function destroy($id)
    {
        try{

            $user = Category::FindOrFail($id);
            $user->delete();
            return response()->json(['code' => 200,'massage' => 'Category delete successfully.','data' => null]);
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
            if ($bug == 1451 ) {
                return response()->json(['code' => 400,'massage' => 'Cannot delete a parent data,this data is used anywhere.','data' => null]);
            } else {
                return response()->json(['code' => 400,'massage' => 'something error found,please try again.','data' => null]);
            }
        }
    }

}
