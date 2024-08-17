<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Users/Index', [
			'users' => User::all()
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
			'role' => 'required'
        ]);

		$newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
			'role' => $request->role
        ]);

		return back()->with('flash', $newUser);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (auth()->user()->id == $user->id) return to_route('profile.edit');
		if ($user->id == 1) abort(403);

		return inertia('Users/Edit', [
			'user' => $user
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
		if (auth()->user()->id == $user->id) return;
		if ($user->id == 1) abort(403);

		$validationRules = [
			'name' => 'required|string|max:255',
			'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
			'role' => 'required'
		];
		if ($request->password) $validationRules['password'] = ['required', 'confirmed', Rules\Password::defaults()];

		$request->validate($validationRules);

		$updateData = [
			'name' => $request->name,
            'email' => $request->email,
			'role' => $request->role
		];
		if ($request->password) $updateData['password'] = Hash::make($request->password);

		$user->update($updateData);

		return back()->with(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
		if (auth()->user()->id == $user->id) return;
		if ($user->id == 1) abort(403);

        $user->delete();
		return to_route('users')->with('flash', 'deleted');
    }
}