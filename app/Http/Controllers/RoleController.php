<?php

namespace App\Http\Controllers;

use Auth;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function __construct(){
        $this->user =  Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $roles = Role::orderBy('id', 'ASC')->get();
      return view('roles.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
      $permissions = Permission::orderBy('id', 'asc')->get();
      $perms       = $role->permissions()->get()->pluck('id')->toArray();
      return view('roles.edit', ['role' => $role, 'permissions' => $permissions, 'perms' => $perms]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
      $request->validate([
          'name' => 'required',
      ]);

      if( $request->exists('permissions') && $request->permissions != NULL ){
        $permissions = $request->permissions;
        $role->perms()->sync([]);
        $role->attachPermissions($permissions);
      }

      $request->request->remove('permissions');
      $role->update($request->all());

      return redirect()->route('roles.index')
                      ->with('success', __('Role Updated Successfully!') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
    }
}
