<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function __construct(){
        $this->user =  Auth::user();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        switch(Auth::user()->roles->first()->id)
        {
            case 1: $users = User::all(); break;

            case 2: $users = $user->users; $users = $users->merge(User::whereHas('roles', function($q) {$q->where('id', '>', '2');})->whereHas('projects', function($q) use ($user) {$q->whereIn('id', $user->projects()->pluck('id')); })->get());  break;

            case 3: $users = $user->users; $users = $users->merge(User::whereHas('roles', function($q) {$q->where('id', '>', '3');})->whereHas('projects', function($q) use ($user) {$q->whereIn('id', $user->projects()->pluck('id')); })->get());  break;

            default: break;
        }

        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $rules = [
            'name'      => 'required|max:255|unique:users,name',
            'email'     => 'required|string|email:rfc,dns|unique:users,email',
            'password'  => ['required', 'string', 'min:8',  'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',  'regex:/[@$!%*#?&]/',],
            'role'      => 'required|numeric',
            'user_img'  => 'image|nullable',
        ];

        $customMessages = [
            'regex' => __('The password must contain at least 1 upper/lower case letter, a number and a special symbol.')
        ];

        $request->validate($rules, $customMessages);

        $user = User::create([
            'name'              => $request->get('name'),
            'email'             => $request->get('email'),
            'password'          => $request->get('password'),
            'clear_password'    => $request->get('password'),
        ]);

        if(!is_null($request->user_img))
        {
            $file = pathinfo($request->user_img->getClientOriginalName());

            $user_img = str_replace(' ', '_', $file['filename']) . '_' . $user->id . '.' . $request->user_img->getClientOriginalExtension();

            $user->user_img = $user_img;
            $user->save();

            $request->file('user_img')->storeAs("public/users/", $user_img);
        }

        $user->created_by = Auth::user()->id;
        $user->company = $request->get('company');
        $user->save();

        $user->roles()->attach($request->get('role'));

        return redirect()->route('users.index')->with('success', __('User Added Successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.edit', ['user' => $user, 'roles' => $roles, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|unique:users,name,' . $user->id,
            'password'  => ['string', 'min:8',  'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',  'regex:/[@$!%*#?&]/', 'nullable'],
            'email' => 'required',
            'user_img'  => 'image',
        ];

        $customMessages = [
            'regex' => __('The password must contain at least 1 upper/lower case letter, a number and a special symbol.')
        ];

        $request->validate($rules, $customMessages);

        if( $request->exists('password') && $request->password == NULL )
        {
            $request->request->remove('password');
            $user->update($request->all());
        }
        else
        {
            $ps = $request->get('password');
            $request->merge(['clear_password' => $ps]);

            $user->update($request->all());
        }
        $user->company = $request->get('company');
        $user->save();

        if(!is_null($request->user_img))
        {
            Storage::delete("public/users/" . $user->user_img);

            $file = pathinfo($request->user_img->getClientOriginalName());

            $user_img = str_replace(' ', '_', $file['filename']) . '_' . $user->id . '.' . $request->user_img->getClientOriginalExtension();

            $user->user_img = $user_img;
            $user->save();

            $request->file('user_img')->storeAs("public/users/", $user_img);
        }

        if(!is_null($request->role))
        {
            $user->roles()->detach();
            $user->roles()->attach($request->role);
        }

        return redirect()->route('users.index')->with('success', __('User Updated Successfully!') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($user_id)
    {
        $user = User::find($user_id);

        if(is_null($user))
        {
            return redirect()->route('users.index')->with('error', __('User already Deleted or not Existing') );
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', __('User Deleted Successfully') );
    }

    function fetch(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $role = $request->get('role');
            $project = $request->get('project');

            if($role == 0)
            {
                $data = User::whereHas('roles', function($q) use($role) {$q->whereNotIn('id', ['1', '5']);});
            }
            else
            {
                $data = User::whereHas('roles', function($q) use($role) {$q->where('id', $role);});
            }

            if($project != 0)
            {
                $data = $data->whereHas('projects', function($q) use($project) {$q->where('id', $project);});
            }

            $data = $data->where('name', 'LIKE', "%{$query}%")->get();

            $output = '<ul class="collection" style="display:block; position:relative">';
            foreach($data as $row)
            {
                if($project != 0)
                {
                    $output .= '<li class="collection-item" data-ref="user" data-role="' . $role . '" data-project="' . $project . '" data-value="' . $row->id . '"><a href="#">' . $row->name . '</a></li>';
                }
                else
                {
                    $output .= '<li class="collection-item" data-ref="user" data-role="' . $role . '" data-value="' . $row->id . '"><a href="#">' . $row->name . '</a></li>';
                }
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function fetch_company(Request $request)
    {
        $query = $request->get('query');

        $data = User::select('company')->where('company', 'LIKE', "%{$query}%")->groupBy('company')->get();

        $output = '<ul class="collection" style="display:block; position:relative">';
        foreach($data as $row)
        {
            $output .= '<li class="collection-item" data-ref="company" data-value="' . $row->company . '"><a href="#">' . $row->company . '</a></li>';
        }
        $output .= '</ul>';
        echo $output;

    }

    public function log($user_id)
    {
        $user = User::find($user_id);
        $logs = $user->authentications;

        $content = $user->name . "\n";
        $rows = [];

        foreach($logs as $key => $log)
        {
            $rows[] = '- ' . date('d/m/Y H:i:s', strtotime($log->login_at));
        }
        $rows = array_reverse($rows);
        $content .= implode("\n", $rows);

        Storage::put('user.log', $content);
        return Storage::download('user.log');
    }
}
