<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::with(['organisation', 'division']);

        if ($user->role === 'leader') {
            $query->where('division_id', $user->division_id);
        }

        $users = $query->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'leader') {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
            $divisions = Division::where('id', $user->division_id)->get();
        } else {
            $organisations = Organisation::all();
            $divisions = Division::all();
        }
        return view('users.create', compact('organisations', 'divisions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'unique:users,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!in_array($domain, $allowedDomains)) {
                        $fail('The email must belong to a prestatiprima domain.');
                    }
                },
            ],
            'password' => 'required|min:8',
        ];

        if ($user->role === 'admin') {
            $rules['role'] = 'required|in:admin,leader,member';
            $rules['organisation_id'] = 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($user->role === 'leader') {
            $data['role'] = 'member';
            $data['organisation_id'] = $user->organisation_id;
            $data['division_id'] = $user->division_id;
        } else {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->organisation_id;
            $data['division_id'] = $request->division_id;
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $authUser = auth()->user();
        if ($authUser->role === 'leader' && $user->division_id !== $authUser->division_id) {
            abort(403);
        }

        if ($authUser->role === 'leader') {
            $organisations = Organisation::where('id', $authUser->organisation_id)->get();
            $divisions = Division::where('id', $authUser->division_id)->get();
        } else {
            $organisations = Organisation::all();
            $divisions = Division::all();
        }
        return view('users.edit', compact('user', 'organisations', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();
        if ($authUser->role === 'leader' && $user->division_id !== $authUser->division_id) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', Rule::unique('users')->ignore($user->id),
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!in_array($domain, $allowedDomains)) {
                        $fail('The email must belong to a prestatiprima domain.');
                    }
                },
            ],
            'password' => 'nullable|min:8',
        ];

        if ($authUser->role === 'admin') {
            $rules['role'] = 'required|in:admin,leader,member';
            $rules['organisation_id'] = 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($authUser->role === 'admin') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->organisation_id;
            $data['division_id'] = $request->division_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $authUser = auth()->user();
        if ($authUser->role === 'leader' && $user->division_id !== $authUser->division_id) {
            abort(403);
        }

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
