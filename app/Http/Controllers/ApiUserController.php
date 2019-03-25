<?php

namespace App\Http\Controllers;

use JWTAuth;

use App\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    public function index()
    {
        $user = User::get();
        if(count($user) > 0) {
            return response()->json(['code' => 200, 'massage' => 'success','data' => $user]);
        }
        return response()->json(['code' => 200,'massage' => 'Data Not found','data' => []]);
    }

    public function store(Request $request)
    {
        // dd(JWTAuth::getToken(),$request->header('authorization'),$request->all());
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required|unique:users,email',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'       => 422,
                'massage'    => apiValidateError($validator->errors()),
                'data'       => null
            ]);
        }

        $request->request->add(['password' => Hash::make($request->password)]);

        try{
            $result = User::create($request->all());
            return response()->json(['code' => 200,'massage' => 'User registration successfully.','data' => $result]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'User registration failed,something error found','data' => null]);
        }
    }

    public function edit($id)
    {
        $editModeData = user::FindOrFail($id);
        if( $editModeData ){
            return response()->json(['code' => 200,'massage' => 'success','data' => $editModeData]);
        }
        return response()->json(['code' => 200,'massage' => 'Data not found','data' => null]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 =>'required|unique:users,email,'.$id.',id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'       => 422,
                'massage'    => apiValidateError($validator->errors()),
                'data'       => null
            ]);
        }

        $data = user::FindOrFail($id);
        try{
            $data->update($request->all());
            return response()->json(['code' => 200,'massage' => 'User successfully updated.','data' => $data]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'something error found,please try again','data' => null]);
        }
    }

    public function destroy($id)
    {
        try{

            $user = User::FindOrFail($id);
            $user->delete();
            return response()->json(['code' => 200,'massage' => 'User delete successfully.','data' => null]);
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
            if ($bug == 1451 ) {
                return response()->json(['code' => 400,'massage' => 'Cannot delete a parent data,this data is used anywhere','data' => null]);
            } else {
                return response()->json(['code' => 400,'massage' => 'something error found,please try again','data' => null]);
            }
        }
    }
	
	public function findAuthUser()
    {
		$user_id = JWTAuth::toUser(JWTAuth::getToken())['id'];
        $user = User::where('id',$user_id)->first();
        if($user) {
            return response()->json(['code' => 200, 'massage' => 'success','data' => $user]);
        }
        return response()->json(['code' => 200,'massage' => 'Data Not found','data' => []]);
    }
	

}
