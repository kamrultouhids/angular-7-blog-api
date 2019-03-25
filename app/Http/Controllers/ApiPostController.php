<?php

namespace App\Http\Controllers;

use File;

use JWTAuth;

use App\Post;

use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Validator;


class ApiPostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['created_by','category'])->get();

        $data = [
            'image_url' => url('/').'/upload/post_image/',
            'posts' => $posts
        ];
        if(count($posts) > 0) {
            return response()->json(['code' => 200, 'massage' => 'success','data' => $data]);
        }
        return response()->json(['code' => 200,'massage' => 'Data Not found','data' => []]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'                     => 'required|unique:posts',
            'post_slug'                 => 'required|unique:posts',
            'category_id'               => 'required',
            'description'               => 'required',
            'date'                      => 'required',
            'picture'                   => 'required',
            'status'                    => 'required',
        ],[
            'category_id.required'      => 'The category field is requierd.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'       => 422,
                'massage'    => apiValidateError($validator->errors()),
                'data'       => null
            ]);
        }
		
        try{
            $new = new Post();

            if ($request->picture) {

                $folderPath = 'upload/post_image';
                $filename = md5(str_random(10).time());

                if(!File::exists(public_path($folderPath))) {
                    File::makeDirectory(public_path().'/'.$folderPath , 0777 , true, true);
                }

                $data = $request->picture;
                //get the base-64 from data
                $data = str_replace('data:image/png;base64,', '', $data);
                $data = str_replace('data:image/jpeg;base64,', '', $data);
                $data = str_replace('data:image/jpg;base64,', '', $data);
                $data = str_replace('data:image/gif;base64,', '', $data);

                $imgData = explode(',',$data);
                $img = $imgData[0];
                $base64_str = str_replace(' ', '+', $img);

                $image = base64_decode($base64_str);

                Image::make($image)->resize(750, 200)->save(public_path($folderPath .'/'.$filename. '.png'));

                $new->picture = $filename.'.png';
            }

            $user_id = JWTAuth::toUser(JWTAuth::getToken())['id'];

            $new->category_id = $request->category_id;
            $new->title = $request->title;
            $new->description = $request->description;
            $new->date = $request->date;
            $new->created_by = $user_id;
            $new->post_slug = $request->post_slug;
            $new->status = $request->status;
            $new->save();

            return response()->json(['code' => 200,'massage' => 'Post successfully saved.','data' => $new]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'something error found, please try again!','data' => null]);
        }
    }

    public function edit($id)
    {
        $editModeData = Post::FindOrFail($id);

        $data = [
            'image_url' => url('/').'/upload/post_image/',
            'editModeData' => $editModeData
        ];

        if( $editModeData ){
            return response()->json(['code' => 200,'massage' => 'success','data' => $data]);
        }
        return response()->json(['code' => 200,'massage' => 'Data not found','data' => null]);
    }


    public function update(Request $request,$id)
    {
       $validator = Validator::make($request->all(), [
           'title'                     => 'required|unique:posts,title,'.$id.',id',
           'post_slug'                 => 'required|unique:posts,post_slug,'.$id.',id',
           'category_id'               => 'required',
           'description'               => 'required',
           'date'                      => 'required',
           'status'                    => 'required',
       ],[
           'category_id.required'      => 'The category field is requierd.',
       ]);

       if ($validator->fails()) {
           return response()->json([
               'code'       => 422,
               'massage'    => apiValidateError($validator->errors()),
               'data'       => null
           ]);
       }

        try{
            $new = Post::FindOrFail($id);
            if ($request->newPicture) {

                $folderPath = 'upload/post_image';
                $filename = explode('.',$new->picture)[0];

                if(!File::exists(public_path($folderPath))) {
                    File::makeDirectory(public_path().'/'.$folderPath , 0777 , true, true);
                }

                $data = $request->newPicture;
                //get the base-64 from data
                $data = str_replace('data:image/png;base64,', '', $data);
                $data = str_replace('data:image/jpeg;base64,', '', $data);
                $data = str_replace('data:image/jpg;base64,', '', $data);
                $data = str_replace('data:image/gif;base64,', '', $data);

                $imgData = explode(',',$data);
                $img = $imgData[0];
                $base64_str = str_replace(' ', '+', $img);

                $image = base64_decode($base64_str);

                Image::make($image)->resize(750, 200)->save(public_path($folderPath .'/'.$filename. '.png'));

                $new->picture = $filename.'.png';
            }

            $new->category_id = $request->category_id;
            $new->title = $request->title;
            $new->description = $request->description;
            $new->date = $request->date;
            $new->post_slug = $request->post_slug;
            $new->status = $request->status;
            $new->save();

            return response()->json(['code' => 200,'massage' => 'Post successfully updated.','data' => $new]);
        }
        catch(\Exception $e){
            return response()->json(['code' => 400,'massage' => 'something error found, please try again!','data' => null]);
        }
    }

    public function destroy($id)
    {
        try{
            $data = Post::FindOrFail($id);
            if (!is_null($data->picture)) {
                if(file_exists('upload/post_image/'.$data->picture) AND !empty($data->picture)){
                    unlink('upload/post_image/'.$data->picture);
                }
            }
            $data->delete();
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
