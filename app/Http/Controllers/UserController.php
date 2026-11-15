<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $user = Role::where('slug', 'user')->first();
        return view('admin.master-data.user.index', [
            'title' => 'Users',
            'active' => 'user',
            'datas' => User::where('role_id', $user->id)->get(),
        ]);
    }

    /**
     * Show the form for creating a new user.
    */
    public function create()
    {
        return view('admin.master-data.user.create', [
            'title' => 'Users',
            'subtitle' => 'Add user',
            'active' => 'user',
            'roles' => Role::where('slug', 'user')->get(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Name is required!',
            'email.required' => 'Email is required!',
            'password.required' => 'Password is required!',
        ]);

        // Find role by slug 'user'
        $role = Role::where('slug', 'user')->firstOrFail();

        // Create user
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $role->id,
        ]);

        return redirect()->route('admin.user')->with('success', 'User has been added!');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.master-data.user.edit', [
            'title' => 'Users',
            'active' => 'user',
            'user' => $user,
            'subtitle' => 'Edit user',
            'roles' => Role::where('slug', 'user')->get(),
        ]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);

        // Update role by slug 'user'
        $role = Role::where('slug', 'user')->firstOrFail();

        // Update user details
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $role->id,
            'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
        ]);

        return redirect()->route('admin.user')->with('success', 'User has been updated!');
    }

    /**
     * Remove a user.
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.user')->with('success', 'User has been deleted!');
    }
}
