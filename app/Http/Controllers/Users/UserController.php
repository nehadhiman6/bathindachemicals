<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Auth\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // if($request->headers->has('x-inertia')){
            return Inertia::render('ProjectComponents/Users/UserList', [
                'roles' => Role::all()->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
            ]);
        // }

    }

    public function userslist(Request $request){
        $count = User::count();
        $filteredCount = $count;

        $users = User::where('name','!=', 'Infoway')->where('email','!=','infowayit@infowayindia.com');

        if ($searchStr = $request->input('search.value')) {
            $users = $users->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $users = $users->orderBy($field_name, $asc_desc);
        }
        else{
            $users = $users->orderBy('id','DESC');

        }

        $users = $users->take($request->length);
        $filteredCount = $users->count();
        if ($request->start > 0) {
            $users->skip($request->start);
        }
        $users = $users->select()->with('roles','user_branches.branch','user_companies.company','report_user_branches.branch')->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $users,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function validateForm($request){
        $rules =[
            'name' => 'required|max:255',
            'role_id' => 'required|integer|min:0',
            'email' => 'required|email|max:255|unique:users,email,' . $request['form_id'],
        ];
        if($request->form_id ==0){
            $rules+=[
                'password' => 'required|min:6|confirmed',
            ];
        }
        else if(isset($request->password) &&  $request->password != ''){
            $rules+=[
                'password' => 'required|min:6|confirmed',
            ];
        }
        $this->validate($request, $rules);


    }

    public function store(Request $request){
        $this->validateForm($request);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
        ]);
        $user = UserRole::create([
            'user_id' => $user->id,
            'role_id' =>  $request->role_id
        ]);

        // $user = UserProfile::create([
        //     'user_id' => $user->id,
        //     'leave_date' => $request->leave_date,
        //     'joining_date' => $request->joining_date,
        // ]);

        return reply(true,[
            'user'=>$user
        ]);
    }

    public function edit($id){
        $user = User::findOrFail($id);
        if($user){
            $user->load('roles');
        }
        return reply(true,[
            'user'=>$user ]);
    }

    public function update(Request $request,$id){
        $this->validateForm($request);
        $user = User::findOrFail($id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        if (isset($request['password']) && $request['password']  != '') {
            $user->password = Hash::make($request['password']);
        }

        $user->update();
        if (UserRole::where('user_id', $user->id)->first()) {
            $user_role = UserRole::where('user_id', $user->id)->first()->update(['role_id' => $request->role_id]);
        } else {
            $user = UserRole::create([
                'user_id' => $user->id,
                'role_id' =>  $request->role_id
            ]);
        }
        return reply(true,[
            'user'=>$user ]);
    }
}
