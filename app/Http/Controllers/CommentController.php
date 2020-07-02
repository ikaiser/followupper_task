<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Datacuration;
use App\DatacurationElement;
use App\Mail\NewComment;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($project_id, $file_id)
    {
        return view('comment.create', ['project' => $project_id, 'file' => $file_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $project_id, $file_id)
    {
        $request->validate([
            'comment'   => 'required',
        ]);

        $parent = $request->get('parent');
        $user = Auth::user();
        $comment= new Comment;

        $comment->comment = $request->get('comment');
        $comment->user_id = $user->getAuthIdentifier();
        $comment->file_id = $file_id;
        if(!is_null($parent))
        {
            $comment->comment_id = $parent;
        }

        $comment->save();

        $response = array
        (
            'status'    => 'success',
            'id'        => $comment->id,
            'data'      => $comment->comment,
            'user'      => $user->name,
            'img'       => !is_null($user->user_img) ? Storage::url("users/") . $user->user_img : ''
        );

        $dce = DatacurationElement::find($comment->file_id);
        $project = Project::find($dce->project_id);

        $users = $project->users()->whereHas('roles', function ($q) {$q ->whereIn('id', [2, 3]);})->get();

        foreach($users as $user)
        {
            Mail::to($user)->send(New NewComment($user, $comment));
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param $project
     * @param $file_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($project, $file_id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($project, $file_id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $project, $file_id)
    {
        $request->validate([
            'comment'   => 'required',
        ]);

        $comment_id = $request->get('id');
        $comment= Comment::find($comment_id);

        $comment->comment = $request->get('comment');
        $comment->save();

        $response = array
        (
            'status'    => 'success',
        );

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $project_id
     * @param $file_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $project_id, $file_id)
    {
        $comment = Comment::find($request->get('comment'));
        remove_childs_comments($comment->id);
        $comment->delete();

        $response = array
        (
            'status' => 'success',
        );

        return response()->json($response);
    }
}
