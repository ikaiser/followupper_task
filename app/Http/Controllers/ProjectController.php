<?php

namespace App\Http\Controllers;

use App\Project;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $role = Auth::user()->roles->first();

        if($role->id == 1)
        {
            $projects = Project::all();
        }
        else
        {
            $projects = $user->projects()->get();
        }

        //$search_projects = Project::has('files')->get();
        $authors = User::has('files')->get();

        /*
        $tags = DB::table('datacuration_element')
            ->select('tags')->get();
        $tags_list = [];

        foreach($tags as $tag)
        {
            foreach(explode(';', $tag->tags) as $single_tag)
            {
                $tags_list[] = strtolower($single_tag);
            }
        }
        $tags_list = array_unique($tags_list);
        */

        $data = array(
            'authors' => $authors,
            //'projects' => $search_projects,
            //'tags' => $tags_list
        );

        return view('projects.index', ['projects' => $projects, 'data' => $data, 'role' => $role]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $project = new Project;
      return view('projects.create', ['project' => $project]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
        'name'=>'required',
      ]);

      $user = Auth::user();
      $project = new Project();
      $project->name = $request->get('name');
      $project->save();

      if(!is_null($request->logo))
      {
          $file = pathinfo($request->logo->getClientOriginalName());

          $logo = str_replace(' ', '_', $file['filename']) . '.' . $request->logo->getClientOriginalExtension();

          $project->logo = $logo;
          $project->save();

          $request->file('logo')->storeAs("public/project/{$project->id}/", $logo);
      }

      if($user->roles->first()->id == 2)
      {
          $project->users()->attach($user->id);
      }

      return redirect()->route('projects.index')->with('success', __('Project Added Successfully!') );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Project $project
     * @return void
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
      return view('projects.edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
      $request->validate([
          'name' => 'required',
      ]);

      $project->name = $request->get('name');

      if(!is_null($request->logo))
      {
          Storage::delete("public/project/{$project->id}/" . $project->logo);

          $file = pathinfo($request->logo->getClientOriginalName());

          $logo = str_replace(' ', '_', $file['filename']) . '.' . $request->logo->getClientOriginalExtension();

          $project->logo = $logo;

          $request->file('logo')->storeAs("public/project/{$project->id}/", $logo);
      }

      $project->save();

      return redirect()->route('projects.index')->with('success', __('Project Updated Successfully!') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $project = Project::find($id);
        if($project->dc()->exists())
        {
            return redirect()->route('projects.index')->with('error', __('You can\'t remove a project that has a room inside.') );
        }

        $project->users()->detach();
        $project->delete();
        return redirect()->route('projects.index')->with('message', __('Project Deleted Successfully!') );
    }

    /**
     * Assign users to the project
     *
     * @param $project_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function assign_users($project_id)
    {
        $project = Project::find($project_id);
        $users = array();
        $roles = Role::where('id', '>', 1)->get();

        foreach($roles as $role)
        {
            $role_users = User::whereHas('roles', function($q) use($role) {
                $q->where('id', $role->id);
            })->get();

            $user_group = array(
                'role_id'   => $role->id,
                'users'     => $role_users
            );

            $users[$role->name] = $user_group;
        }

        return view('projects.assign', ['project' => $project, 'users' => $users]);
    }

    /**
     * @param Request $request
     * @param $project_id
     * @return \Illuminate\Http\RedirectResponse
     */
    function save_users(Request $request, $project_id)
    {
        $user = Auth::user();
        $role = $user->roles->first()->id;
        $project = Project::find($project_id);

        if($role == 2)
        {
            $detach_users = $project->users()->whereHas('roles', function($q) {$q->where('id', '>', '2');})->pluck('id')->toArray();
            $project->users()->detach($detach_users);
        }
        elseif($role == 3)
        {
            $detach_users = $project->users()->whereHas('roles', function($q) {$q->where('id', '4');})->pluck('id')->toArray();
            $project->users()->detach($detach_users);
        }
        else
        {
            $project->users()->detach();
        }

        $users = $request->get('users');
        $users = array_unique($users);

        foreach($users as $user)
        {
            if(is_null($user))
            {
                continue;
            }

            $project_user = User::where('name', $user)->get()->first();
            $project_user->projects()->attach($project_id);
        }

        return redirect()->route('projects.index')->with('message', __('Users Assigned Successfully to the Project!') );
    }

    function fetch(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $user = Auth::user();
            if($user->roles->first()->id == 1)
            {
                $data = Project::Where('name', 'LIKE', "%{$query}%")->has('files')->get();
            }
            else
            {
                $data = $user->projects()->Where('name', 'LIKE', "%{$query}%")->has('files')->get();
            }
            $output = '<ul class="collection" style="display:block; position:relative">';
            foreach($data as $row)
            {
                $output .= '<li class="collection-item" data-ref="states" data-value="' . $row->id . '"><a href="#">' . $row->name . '</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
}
