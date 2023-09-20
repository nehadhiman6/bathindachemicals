<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Models\Auth\RolePermission;
use App\Models\Auth\PermissionGroup;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('ProjectComponents/Roles/RoleList', [
            'roles' => Role::all()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ]);

    }


    public function roleslist(Request $request){
        return reply(true,[
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/|unique:roles',
        ]);
        $role = Role::create($request->only(['name', 'label']));
        return reply(true,[
            'roles' => $role
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $roles = Role::orderBy('name')->get();
        return view('auth.roles.index', compact('roles', 'role'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/|unique:roles,name,' . $id,
        ]);
        $role = Role::findOrFail($id);
        $role->fill($request->all());
        $role->save();
        return redirect('roles');
    }


    public function showPermissions($role_id)
    {
        $role = Role::find($role_id);
        $role->load(['permissions']);
        $permissions =  $role->permissions->pluck('id')->toArray();


        $opt_groups = PermissionGroup::orderBy('order_no')
            ->with(['permissions'])
            ->get();


        return Inertia::render('ProjectComponents/Roles/RolePermission', [
            'role'=> $role,
            'opt_groups'=> $opt_groups,
            'permissions' => $permissions,
            'add' =>false
        ]);
    }

    public function addRole($role_id)
    {
        $role = Role::find($role_id);
        $role->load(['permissions']);
        $permissions =  $role->permissions->pluck('id')->toArray();


        $opt_groups = PermissionGroup::orderBy('order_no')
            ->with(['permissions'])
            ->get();


        return Inertia::render('ProjectComponents/Roles/RolePermission', [
            'role'=> $role,
            'opt_groups'=> $opt_groups,
            'permissions' => $permissions,
            'add' =>true
        ]);
    }


    public function savePermissions(Request $request, $role_id)
    {
        $rules =  [
            'permissions' => 'array|min:1',
        ];

        if($request->add == true){
            $rules +=  [
                'name' => 'required',
            ];

        }
        $this->validate($request,$rules, [
            'permissions.size' => 'Select atleast one permission to save!!'
        ]);

        $role = Role::findOrFail($role_id);
        if($request->add){
            $role = new Role();
            $role->name = $request['name'];
            $role->save();
        }


        $role->permissions()->sync($request->permissions);
        // $role_permission_ids = RolePermission::where('role_id', $role_id)->pluck('permission_id')->toArray();
        // if ($request->permission == null) {
        //     $request->permission = [];
        // }
        // $diff = array_diff($role_permission_ids, $request->permission);

        // foreach ($request->input('permission', []) as $permis) {
        //     $role_permission = RolePermission::where('role_id', $role_id)->where('permission_id', $permis)->get();
        //     if ($role_permission == '[]') {
        //         $permission = new RolePermission();
        //         $permission->create(['role_id' => $role_id, 'permission_id' => $permis]);
        //     }
        // }
        // RolePermission::where('role_id', $role_id)->whereIn('permission_id', $diff)->delete();
        return reply(true);
    }
}
