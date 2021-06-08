<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Image;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create user', ['only' => ['create', 'store']]);
        $this->middleware('can:edit user', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete user', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $search = request('search', null);
        $users = User::when($search, function($user) use($search) {
            return $user->where("name", 'like', '%' . $search . '%')
            ->orWhere('id', $search);
        })->paginate();
        $users->load('roles');
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $input = $request->only('name', 'email', 'password');
        $input['password'] = bcrypt($request->password);
        $user = User::create($input);
        $user->assignRole($request->role);
        //return redirect()->route('admin.user.index')->with('success', 'A user was created.');
        return response()->json([
            'success' => 'A team member was created successfully.' // for status 200
        ]);
    }

    public function show()
    {
        return redirect()->route('admin.user.index');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        $userRole = $user->getRoleNames()->first();
        return view('admin.user.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $input = $request->only('name', 'email');
        if($request->filled('password')) {
            $input['password'] = bcrypt($request->password);
        }
        $user->update($input);
        $user->syncRoles($request->role);
        //return redirect()->route('admin.user.index')->with('success', 'A user was updated.');
        return response()->json([
            'success' => 'A team member was updated successfully.' // for status 200
        ]);
    }

    public function destroy(User $user)
    {
        if(auth()->id() === $user->id) {
            //return back()->withErrors('You cannot delete current logged in user.');
            return response()->json([
                'errors' => 'You cannot delete current logged in user.' // for status 200
            ]);
        }
        $user->delete();
        //return redirect()->route('admin.user.index')->with('success', 'A user was deleted.');
        return response()->json([
            'success' => 'A team member was deleted successfully.' // for status 200
        ]);
    }

    public static function getImageUrlAttribute($id)
    {
        $image = Image::where('imageable_id', $id)->first();
        if(isset($image->filename)) {
            return asset('/storage/app/public/image/profile/'. $image->filename);
        }else{
            return asset('/public/image/default_profile.jpg');
        }
    }
}