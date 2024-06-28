<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\Update;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Image;
use App\Models\Branch;
class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.profile.profile', ['user' => Auth::user(), 'branches' => Branch::orderBy('name')->get()]);
    }
    public function updateProfile(Update $request, User $user)
    {

        $user->fill($request->all());

        if ($user->save()) {

            session()->flash('app_message', 'Profile successfully updated');
            return redirect()->route('profile');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Profile');
        }
        return redirect()->back();
    }
    public function changePassword(Request $request)
    {

        if($request->npwd != $request->npwd_confirmation)
            return back()->with('app_error', 'Password didnt match.');
        $user = User::find(Auth::id());
        $hashedPassword = $user->password;
        if (Hash::check($request->oldpwd, Auth::user()->password)) {
            $user->password = bcrypt($request->npwd);
            $user->save();
            session()->flash('app_message', 'Your password has been changed.');
            return redirect()->back();
        }
        session()->flash('app_message', 'Password cannot be changed.');
        return redirect()->back();

    }
    public function uploadPhoto1(Request $request)
    {
        if ($request->hasFile('image')) {
            //$filename = $request->image->getClientOriginalName();
            $extension = $request->image->getClientOriginalExtension();
            $filename = Auth::id() . "." . $extension;
            $request->image->storeAs('staffpics', $filename, 'public');
            $id = Auth::user()->id;
            $user = User::find($id);
            $user->photo = $filename;
            $user->save();
        //$user->update(['photo'=>$filename]);
        }
        return redirect()->back();
    }
    public function uploadPhoto(Request $request)
    {
        $request->session()->keep('backurl');
        if ($request->hasFile('image')) {
            $file = request()->file('image');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Auth::id() . "." . $extension;
            $file->move(public_path('staffpics'), $filename);
            if (file_exists(public_path() . '/staffpics/' . $filename)) {
                $url = 'staffpics/' . $filename;
                $img = Image::make(public_path() . '/staffpics/' . $filename);
                $id = Auth::user()->id;
                $user = User::find($id);
                $user->photo_url = $filename;
                $user->save();
                if ($request->session()->has('backurl')) {
                    return redirect($request->session()->get('backurl'));
                }
                $img->save(public_path() . '/staffpics/' . $filename);
            }
            else {
                $request->session()->keep('backurl');
                $url = 'staffpics/man.jpg';
            }



        //$user->update(['photo'=>$filename]);
        }
        return redirect()->back();
    }
    public function switchBranch(Request $request, User $user)
    {

        $user->branch_id = $request->branch;
        $user->save();
        session()->flash('app_message', 'Branch successfully switched');
        //return redirect()->back();
        return redirect(route('home'));
    }
}
