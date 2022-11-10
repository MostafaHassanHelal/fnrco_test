<?php

namespace App\Http\Controllers;

use App\Concerns\HandlesFiles;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\LikeRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use HandlesFiles;

    public function index(Request $request)
    {
        $postsQuery =  Post::with(['user', 'comments.user', 'likes', 'albums', 'tags:id,title']);
        if (isset($request->tag)) {
            $postsQuery->whereHas('tags', function ($q) use ($request) {
                $q->where('id', $request->tag);
            });
        }
        $postsQuery->orderBy('created_at', "DESC");
        $posts = $postsQuery->get();
        return view("blog", ['posts' => $posts]);
    }

    public function create(CreatePostRequest $request)
    {
        $user = Auth::user();
        $post = $user->posts()->create(
            $request->only('title', 'description')
        );
        if (isset($request->images)) {
            $images = explode(',', $request->images);
            foreach ($images  as $image_url) {
                $image_url = str_replace($this->getDefaultUrl(), "/", $image_url);
                $post->albums()->create([
                    'image' => $image_url,
                    'model_type' => get_class($post),
                    'model_id' => $post->id,
                ]);
            }
        }

        if (isset($request->tags)) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $title) {
                $post->tags()->firstOrCreate(['title' => $title], ['title' => $title]);
            }
        }

        return response()->json(['status' => 'success', "data" => $post]);
    }

    public function delete(DeletePostRequest $request)
    {
        $user = Auth::user();
        $userPost = $user->posts()->where('id', $request->post_id)->first();
        if (!$userPost) {
            return response()->json(['status' => 'fail', 'message' => 'unauthorized']);
        }
        $userPost->delete();
        return response()->json(['status' => 'success']);
    }

    public function comment(CommentRequest $request)
    {
        $user = Auth::user();
        //dd($user->posts()->where('id',$request->post_id)->get());
        $post = Post::find($request->post_id)->comments()->create([
            'content' => $request->content,
            'user_id' => $user->id,
        ]);

        return response()->json(["status:success", "data" => $post]);
    }

    public function like(LikeRequest $request)
    {
        $user = Auth::user();
        //dd($user->posts()->where('id',$request->post_id)->get());
        $post = Post::find($request->post_id)->likes()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return response()->json(["status:success", "data" => $post]);
    }
}
