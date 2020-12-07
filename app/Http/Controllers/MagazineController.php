<?php

namespace App\Http\Controllers;

use App\Models\CategoryMagazine;
use App\Models\Magazine;
use App\Models\Rating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Madnest\Madzipper\Facades\Madzipper;
use ZipArchive;

class MagazineController extends Controller
{

    public $dimenCover;
    public $pathCover;
    public $dataType;
    public $pathImg;
    public $pathDoc;
    public $pathZip;

    public function __construct()
    {
        $this->dataType = 'Magazine';
        $this->dimenCover = 750;
        $this->pathDoc = public_path().'/document/topic/';
        $this->pathImg = public_path().'/images/magazine/';
        $this->pathCover = public_path().'/images/magazine/cover';
        $this->pathZip = public_path().'/zip';
    }

    public function index(Request $request)
    {
        if($request->has('paginate')) {
            $page = $request->paginate;
            $magazine = Magazine::whereNull('deleted_at')->with(['Topic' => function($query) {
                $query->orderBy('id', 'DESC');
            }, 'Rating' => function($query) {
                $query->select('rating', 'user_id', 'magazine_id', 'updated_at');
            }])->orderBy('id', 'DESC')->paginate($page);
            return $this->onSuccess($this->dataType, $magazine);
        }
        $page = 10;
        $magazine = Magazine::whereNull('deleted_at')->with(['Topic' => function($query) {
            $query->orderBy('id', 'DESC');
        }, 'Rating' => function($query) {
            $query->select('rating', 'user_id', 'magazine_id', 'updated_at');
        }])->orderBy('id', 'DESC')->paginate($page);
        return $this->onSuccess($this->dataType, $magazine);
    }

    public function create()
    {
        //
    }

    public function search(Request $request)
    {
        if($request->has('title') || $request->has('category')) {
            $page = 10;
            if($request->has('paginate') && $request->paginate != null) {
                $page = $request->paginate;
            }
            $magazine = Magazine::query();
            if($request->title != '') {
                $data = $magazine->whereNull('deleted_at')->where('name', 'LIKE', '%'.$request->name.'%')->paginate($page);
            }
            if($request->category != '') {
                $data = $magazine->whereNull('deleted_at')->where('category_id', $request->category)->paginate($page);
            }
            return $this->onSuccess($this->dataType, $data);
        }
    }

    public function searchTopic(Request $request, $id)
    {
        $name = $request->name;
        $category = $request->category;
        $magazine = Magazine::with(['Topic' => function($query) use($name, $category){
            if($name != '') {
                $query->where('title', 'LIKE' ,$name.'%');
            }
            if($category != '') {
                $query->where('category_id', $category);
            }
            if($name == '' && $category == '') {
                $query->get();
            }
        }])->find($id);
        return $this->onSuccess($this->dataType, $magazine);
    }

    public function sortedBy(Request $request)
    {
        $sort = $request->sort;
        if($sort == 'DESC') {
            $magz = Magazine::whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        }
        if($sort == 'ASC') {
            $magz = Magazine::whereNull('deleted_at')->orderBy('id', 'ASC')->get();
        }
        if($sort == 'Popular') {
            $magz = Magazine::get();
            $magz = $magz->sortByDesc(function($mag) {
                return $mag->rating->sum('rating');
            });
        }
        return $this->onSuccess($this->dataType, $magz);
    }

    public function mostPopular()
    {
        $magz = Magazine::get();
        $magz = $magz->sortByDesc(function($mag) {
            return $mag->rating->sum('rating');
        });
        return $this->onSuccess($this->dataType, $magz);
    }

    public function downloadAssets(Request $request, $id)
    {
        $magazine = Magazine::with('File', 'Topic')->find($id);
        $imageMagz = $magazine->cover;
        $files = [];
        $files[] = $this->pathCover.'/'.$imageMagz;
        $topicsMagz = $magazine->Topic;
        if(count($topicsMagz) > 0) {
            foreach($topicsMagz as $topicMagz) {
                $files[] = $this->pathImg.'topic/'.$topicMagz->cover;
                $files[] = $this->pathDoc.$topicMagz->file_pdf;
            }
        }
        $filesMagz = $magazine->File;
        if(count($filesMagz) > 0) {
            foreach($filesMagz as $fileMagz) {
                $files[] = $this->pathImg.'lampiran/'.$fileMagz->name;
            }
        }
        Madzipper::make($this->pathZip.'/'.$request->filename)->add($files)->close();
        $headers = ["Content-Type" => 'application/zip'];
        // return Response::download(public_path().'/public/'.$request->get('filename'));
        return response()->download($this->pathZip.'/'.$request->filename, 'magazine.zip', $headers);
    }

    public function daleteRating($id)
    {
        try {
            $user = User::find(Auth::id());
            $rating = Rating::where('magazine_id', $id)->where('user_id', $user->id)->first();
            $rating->delete();
            return $this->onSuccess('Rating', $rating, 'Deleted');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'cover' => 'image|mimes:jpg,png,jpeg',
            'otherCategory' => 'nullable',
            'description' => 'nullable',
        ]);
        $file = $request->file('cover');
        $fileName = 'coverMagazine_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$file->extension();
        try {
            $magazine = new Magazine();
            $magazine->author_id = $request->author;
            $magazine->title = $request->title;
            $magazine->description = $request->description;
            if($request->category == 'Lainnya') {
                $category = new CategoryMagazine();
                $category->name = $request->otherCategory;
                $category->save();
                $magazine->category_id = $category->id;
            } else {
                $magazine->category_id = $request->category;
            }
            if(!File::isDirectory($this->pathCover)) {
                File::makeDirectory($this->pathCover);
            }
            $img = Image::make($file->path());
            $img->resize($this->dimenCover, $this->dimenCover, function($contraint) {
                $contraint->aspectRatio();
            })->save($this->pathCover.'/'.$fileName);
            $magazine->cover = $fileName;
            $magazine->save();
            return $this->onSuccess($this->dataType, $magazine, 'Created');
        } catch (\Exception $e) {
            if(File::exists($this->pathCover.'/'.$fileName)) {
                unlink($this->pathCover.'/'.$fileName);
            }
            return $this->onError($e);
        }
    }

    public function ratingMagazine(Request $request, $id)
    {
        try {
            $user = User::find(Auth::id());
            $rating = new Rating();
            $rating->user_id = $user->id;
            $rating->magazine_id = $id;
            $rating->rating = $request->rating;
            $rating->save();
            return $this->onSuccess('Rating', $rating, 'Added');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function show($id)
    {
        $magazine = Magazine::whereNull('deleted_at')->with(['Topic' => function($query) {
            $query->whereNull('deleted_at');
            $query->orderBy('page', 'ASC');
        }])->find($id);
        return $this->onSuccess($this->dataType, $magazine);
    }

    public function edit(Magazine $magazine)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'nullable',
        ]);
        if($request->hasFile('cover')) {
            $file = $request->file('cover');
            $fileName = 'coverMagazine_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$file->extension();
        }
        try {
            $magazine = Magazine::whereNull('deleted_at')->find($id);
            $magazine->author_id = $request->author;
            $magazine->title = $request->title;
            $magazine->description = $request->description;
            if($request->category == 'Lainnya') {
                $category = new CategoryMagazine();
                $category->name = $request->otherCategory;
                $category->save();
                $magazine->category_id = $category->id;
            } else {
                $magazine->category_id = $request->category;
            }
            if($request->hasFile('cover') && $request->file('cover') != null) {
                $this->validate($request, [
                    'cover' => 'nullable|sometimes|image|mimes:jpg,png,jpeg|nullable',
                    'otherCategory' => 'nullable',
                ]);
                if(!File::isDirectory($this->pathCover)) {
                    File::makeDirectory($this->pathCover);
                }
                if(File::exists($this->pathCover.'/'.$magazine->cover)) {
                    unlink($this->pathCover.'/'.$magazine->cover);
                }
                $img = Image::make($file->path());
                $img->resize($this->dimenCover, $this->dimenCover, function($contraint) {
                    $contraint->aspectRatio();
                })->save($this->pathCover.'/'.$fileName);
                $magazine->cover = $fileName;
            }
            $magazine->save();
            return $this->onSuccess($this->dataType, $magazine, 'Updated');
        } catch (\Exception $e) {
            if($request->hasFile('cover')) {
                if(File::exists($this->pathCover.'/'.$fileName)) {
                    unlink($this->pathCover.'/'.$fileName);
                }
            }
            return $this->onError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $magazine = Magazine::whereNull('deleted_at')->find($id);
            if(File::exists($this->pathCover.'/'.$magazine->cover)) {
                unlink($this->pathCover.'/'.$magazine->cover);
            }
            $magazine->delete();
            return $this->onSuccess($this->dataType, $magazine, 'Destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function deleted()
    {
        $magazine = Magazine::whereNotNull('deleted_at')->get();
        return response()->json(['data' => $magazine]);
        // return $this->onSuccess($this->dataType, $magazine, 'Founded');
    }

    public function recover($id)
    {
        $magazine = Magazine::find($id);
        $magazine->deleted_at = null;
        $magazine->save();
        return $this->onSuccess($this->dataType, $magazine, 'Recovered');
    }

    public function softDestroy($id)
    {
        try {
            $magazine = Magazine::find($id);
            if(File::exists($this->pathCover.'/'.$magazine->cover)) {
                unlink($this->pathCover.'/'.$magazine->cover);
            }
            $magazine->deleted_at = Carbon::now()->toDateTimeString();
            $magazine->save();
            return $this->onSuccess($this->dataType, $magazine, 'Destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }
}
