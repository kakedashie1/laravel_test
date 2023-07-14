<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SignUpController extends Controller
{
    public function index()
    {
        return view('signUp');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required','max:20'],
            'email' => ['required','email',Rule::unique('users')],
            'password' => ['required','min:8'],
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        auth()->login($user);
        return redirect('mypage/posts');
    }
}
