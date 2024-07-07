<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;

class AcountController extends Controller
{
    public function register()
    {
        return view('account.register');
    }

    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        };

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.login')->with('success', 'You have register successfully.');
    }

    public function login()
    {
        return view('account.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('account.profile');
        } else {
            return redirect()->route('account.login')->withInput()->with('error', 'Invalid email or password.');
        };
    }

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return view('account.profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id . ',id',
        ];
        if (!empty($request->image)) {
            $rules['image'] = 'image';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
        }
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Uploading the image here
        if (!empty($request->image)) {
            // Deleting the old image
            File::delete(public_path('uploads/profile/' . $user->image));
            File::delete(public_path('uploads/profile/thumb/' . $user->image));


            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/profile'), $imageName);
            $user->image = $imageName;
            $user->save();

            $manager = new ImageManager(Driver::class);
            $img = $manager->read('uploads/profile/' . $imageName);

            $img->cover(150, 150);
            $img->save(public_path('uploads/profile/thumb/' . $imageName));
        }

        return redirect()->route('account.profile')->with('success', 'Profile updated successfully.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }
}