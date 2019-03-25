<?php

namespace App\Http\Controllers;

use App\Post;

use App\Category;

use Illuminate\Http\Request;

class ApiFrontendController extends Controller
{
	public $paginate = 50;
	
    public function findCategoryList()
    {
        $categorys = Category::get();
        if(count($categorys) > 0) {
            return response()->json(['code' => 200, 'massage' => 'success','data' => $categorys]);
        }
        return response()->json(['code' => 200,'massage' => 'Data Not found','data' => []]);
    }
	
	public function findAndFilterPosts(Request $request)
    {
		if($request->category_id !=''){
			$posts = Post::with(['created_by'])->where('status','Published')->where('category_id',$request->category_id)->orderBy('id','desc')->paginate($this->paginate);
		}elseif($request->keywordSearch !=''){
			$posts = Post::with(['created_by'])->where('status','Published')->where('title', 'like', '%' . $request->keywordSearch . '%')->orderBy('id','desc')->paginate($this->paginate);
		}else{
			$posts = Post::with(['created_by'])->where('status','Published')->orderBy('id','desc')->paginate($this->paginate);
		}
	
		$data = [
            'image_url' => url('/').'/upload/post_image/',
            'posts' => $posts,
        ];
		
		return response()->json(['code' => 200, 'massage' => 'success','data' => $data]);
     
    }
}
