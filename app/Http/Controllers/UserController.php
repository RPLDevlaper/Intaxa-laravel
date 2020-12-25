<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public $dimen;
    public $path;

    public function __construct()
    {
        $this->dimen = 500;
        $this->path = public_path().'/images/user/avatar';
    }

    public function setPassword(Request $request)
    {
        $user = User::find(Auth::id());
        if(Hash::check($request->oldPassword, $user->password)) {
            $user->password = $request->newPassword;
            $user->save();
            return $this->onSuccess('User', $user, 'Updated Password');
        } else {
            return $this->onSuccess('User', null);
        }
    }

    public function signin(Request $request)
    {
        try {
            if(Auth::attempt($request->only(['email', 'password']))) {
                $user = Auth::user();
                return $this->onSuccess('User', $user, 'Logged');
            } else {
                return $this->onSuccess('User', null);
            }
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function signup(Request $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->email_verify_code = Str::random(5);
            $user->api_token = Str::random(80);
            $user->level = $request->level;
            $user->save();
            return $this->onSuccess('User', $user, 'Finish Signup');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function sendVerify(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $txt = [
                'email' => $user->email,
                'username' => $user->name,
                'code' => $user->email_verify_code,
            ];
            Mail::to($user->email)->send(new VerifyEmail($txt));
            return $this->onSuccess('User', $user, 'Email Terkirim');
        } catch(\Exception $e) {
            return $this->onError($e);
        }
    }

    public function verify(Request $request, $token)
    {
        try {
            $code = $request->code;
            $user = User::where('api_token', $token)->first();
            if($code == $user->email_verify_code) {
                $dateNow = Carbon::now();
                $user->email_verified_at = $dateNow;
                return $this->onSuccess('User', $user, 'Email verified');
            } else {
                return $this->onSuccess('User', $user, 'Email');
            }
        } catch(\Exception $e) {
            return $this->onError($e);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if(!File::isDirectory($this->path)) {
                File::makeDirectory($this->path, 0777, true);
            }
            $avatar = $request->file('avatar');
            if($avatar == null) {
                $user->avatar = 'avatar.svg';
                $user->save();
            } else {
                $this->validate($request, [
                    'avatar' => 'image|mimes:jpg,png,jpeg',
                ]);
                $avatarName = 'Avatar_'.str_replace(' ', '_', $user->name).'_'.time().'.'.$avatar->extension();
                if(File::exists($this->path.'/'.$user->cover) && $user->cover !== null && $user->cover !== 'avatar.svg') {
                    unlink($this->path.'/'.$user->cover);
                }
                $avatarImage = Image::make($avatar->path());
                $avatarImage->resize($this->dimen, $this->dimen, function($constraint) {
                    $constraint->aspectRatio();
                })->save($this->path.'/'.$avatarName);
                $user->avatar = $avatarName;
                $user->save();
            }
            return $this->onSuccess('User Avatar', $user, 'Uploaded');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return $this->onSuccess('User', $user, 'Updated');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function showUser($token)
    {
        $user = User::where('api_token', $token)->first();
        return $this->onSuccess('User', $user, 'User ditemukan');
    }

    public function showAuth()
    {
        $user = User::with('Publisher', 'Activity')->find(Auth::id());
        return $this->onSuccess('User', $user, 'User ditemukan');
    }
}
