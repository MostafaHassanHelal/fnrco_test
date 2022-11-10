<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Album;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request ;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    //

    public function uploadImage(Request $request){
        validator([
            "image" => 'required|file'
        ]);
        $url = $request->image->store("images","public");

        $url = $this->getDefaultUrl().$url;
        return response()->json(['status'=>'success', 'url'=>$url]);
    }

    public function index(Request $request){
        $posts =  Post::with(['comments.user','likes','albums','tags']);
    }

    public function create(CreatePostRequest $request){
        $user = Auth::user();
        $post = $user->posts()->create(
            $request->only('title', 'description')
        );
        foreach($request->images as $image_url){
            $image_url = str_replace($this->getDefaultUrl(),"/",$image_url);
            $post->albums()->create([
                'image'=> $image_url,
                'model_type'=>get_class($post),
                'model_id'=>$post->id,
            ]);
        }

        foreach($request->tags as $title){
            $post->tags()->firstOrCreate(['title'=>$title],['title'=>$title]);
        }

        return response()->json(['status'=>'success', "data"=>$post]);
    }

    public function delete(Request $request){
        Post::find($request->post_id)->delete();
        return response()->json(['status'=>'success']);
    }

    public function comment(Request $request){
        $user=Auth::user();
        //dd($user->posts()->where('id',$request->post_id)->get());
        $post = $user->posts()->find($request->post_id)->comments()->create([
            'content' => $request->content,
            'user_id'=> $user->id,
        ]);

        return response()->json(["status:success","data"=>$post]);
    }

    public function like(Request $request){
        $user=Auth::user();
        //dd($user->posts()->where('id',$request->post_id)->get());
        $post = $user->posts()->find($request->post_id)->likes()->create([
            'user_id'=> $user->id,
        ]);

        return response()->json(["status:success","data"=>$post]);
    }

    private function getDefaultUrl(){
        $disk = config("filesystems.default"); 
        $url = config("filesystems.disks.".$disk)['url']."/";
        return $url;
    }
}
