<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use App\Models\RequestPublisher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class PublisherController extends Controller
{

    public $dimen;
    public $path;

    public function __construct()
    {
        $this->dimen = 500;
        $this->path = public_path().'/images/team/logo';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $publisher = Publisher::all();
        return $this->onSuccess('Publisher', $publisher, 'Founded');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function search(Request $request)
    {
        $publisher = Publisher::query();
        $publish = $publisher->with('Req')->where('name', 'LIKE', '%'.$request->name.'%')->get();
        return $this->onSuccess('Publisher', $publish);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $publisher = new Publisher();
            $publisher->name = $request->name;
            $publisher->category = $request->category;
            $publisher->avatar = 'avatar.png';
            $publisher->description = $request->description;
            $publisher->save();
            $user = User::find(Auth::id());
            $user->level = 'Owner';
            $user->publisher_id = $publisher->id;
            $user->save();
            return $this->onSuccess('Publisher', $publisher, 'Publisher Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function reqPublish($id)
    {
        try {
            $publish = Publisher::find($id);
            $user = User::find(Auth::id());
            $req = new RequestPublisher();
            $req->user_id = $user->id;
            $req->publisher_id = $publish->id;
            $req->status = 'Pending';
            $req->save();
            return $this->onSuccess('Request', $req, 'Pending');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function cancelReqPublish($id)
    {
        try {
            $publish = Publisher::find($id);
            $user = User::find(Auth::id());
            $req = RequestPublisher::where('user_id', $user->id)->where('publisher_id', $publish->id)->first();
            $req->delete();
            return $this->onSuccess('Request', $req, 'Cancelled');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function upload(Request $request, $id)
    {
        // try {
            $publisher = Publisher::find($id);
            if(!File::isDirectory($this->path)) {
                File::makeDirectory($this->path, 0777, true);
            }
            $avatar = $request->file('avatar');
            if($avatar == null) {
                $publisher->avatar = 'avatar.svg';
                $publisher->save();
            } else {
                $publisher = Publisher::find($id);
                $this->validate($request, [
                    'avatar' => 'image|mimes:jpg,png,jpeg',
                ]);
                $avatarName = 'Avatar_'.str_replace(' ', '_', $publisher->name).'_'.time().'.'.$avatar->extension();
                if(File::exists($this->path.'/'.$publisher->avatar) && $publisher->avatar !== null && $publisher->avatar !== 'avatar.png') {
                    unlink($this->path.'/'.$publisher->avatar);
                }
                $avatarImage = Image::make($avatar->path());
                $avatarImage->resize($this->dimen, $this->dimen, function($constraint) {
                    $constraint->aspectRatio();
                })->save($this->path.'/'.$avatarName);
                $publisher->avatar = $avatarName;
                $publisher->save();
            }
            return $this->onSuccess('Team Avatar', $publisher, 'Uploaded');
        // } catch (\Exception $e) {
        //     return $this->onError($e);
        // }
    }

    public function getTeammate()
    {
        $user = User::find(Auth::id());
        $teammate = User::where('publisher_id', $user->publisher_id)->paginate(5);
        return $this->onSuccess('Publisher', $teammate, 'Founded');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show(Publisher $publisher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function edit(Publisher $publisher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find(Auth::id());
            if($user->publisher_id != $id) {
                return $this->onSuccess('Publisher', null, 'Bukan milik anda');
            }
            $publisher = Publisher::find($id);
            $publisher->name = $request->name;
            $publisher->category = $request->category;
            $publisher->description = $request->description;
            $publisher->save();
            return $this->onSuccess('Publisher', $publisher, 'Publisher Updated');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publisher $publisher)
    {
        //
    }
}
