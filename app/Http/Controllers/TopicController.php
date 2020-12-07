<?php

namespace App\Http\Controllers;

use App\Models\CategoryTopic;
use App\Models\Files;
use App\Models\Magazine;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class TopicController extends Controller
{

    public $dimenCover;
    public $pathCover;
    public $dataType;
    public $pathPdf;
    public $pathLampiran;
    public $dimenLampiran;

    public function __construct()
    {
        $this->dimenCover = 650;
        $this->pathCover = public_path().'/images/magazine/topic';
        $this->dataType = 'Topic Magazine';
        $this->pathPdf = public_path().'/document/topic';
        $this->dimenLampiran = 400;
        $this->pathLampiran = public_path().'/images/magazine/lampiran';
    }

    public function index(Request $request)
    {
        if($request->has('paginate')) {
            $page = $request->paginate;
            $topic = Topic::with('File')->orderyBy('id', 'DESC')->paginate($page);
            return $this->onSuccess($this->dataType, $topic);
        }
        $page = 10;
        $topic = Topic::with('File')->orderyBy('id', 'DESC')->paginate($page);
        return $this->onSuccess($this->dataType, $topic);
    }

    public function create()
    {
        //
    }

    public function getPdf($name)
    {
        return response($this->pathPdf.'/'.$name)->header('Content-Type', 'application/pdf');
    }

    public function getTopicByCategory($magazineId ,$id)
    {
        $idCategory = $id;
        $magazine = Magazine::with(['Topic' => function($query) use($idCategory, $magazineId) {
                $query->select(['*']);
                $query->where('category_id', $idCategory);
                $query->where('magazine_id', $magazineId);
                $query->whereNull('deleted_at');
            }
        ])->where('id', $magazineId)->first();
        return $this->onSuccess($this->dataType, $magazine);
    }

    public function store(Request $request)
    {
        try {
            $topic = new Topic();
            $topic->display_mode = $request->display;
            $topic->magazine_id = $request->magazine;
            $topic->page = $request->page;
            $topic->title = $request->title;
            $topic->description = $request->description;
            if($request->category == 'Lainnya') {
                $category = new CategoryTopic();
                $category->name = $request->otherCategory;
                $category->save();
                $topic->category_id = $category->id;
            } else {
                $topic->category_id = $request->category;
            }
            if($request->hasFile('cover') && $request->file('cover') != null) {
                $coverFile = $request->file('cover');
                $coverName = 'coverTopic_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$coverFile->extension();
                $coverImg = Image::make($coverFile->path());
                if(!File::isDirectory($this->pathCover)) {
                    File::makeDirectory($this->pathCover, 0777, false);
                }
                $coverImg->resize($this->dimenCover, $this->dimenCover, function($constraint) {
                    $constraint->aspectRatio();
                })->save($this->pathCover.'/'.$coverName);
                $topic->cover = $coverName;
            } else {
                $topic->cover = 'avatar.png';
            }
            if($request->hasFile('filePdf') && $request->file('filePdf') != null) {
                if(!File::isDirectory($this->pathPdf)) {
                    File::makeDirectory($this->pathPdf, 0777, false);
                }
                $file = $request->file('filePdf');
                $fileName = 'pdfFile_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$file->extension();
                $file->move($this->pathPdf, $fileName);
                $topic->file_pdf = $fileName;
            } else {
                $topic->file_pdf = 'dummy.pdf';
            }
            $topic->save();
            if($request->hasFile('images') && $request->file('images') != null) {
                $images = $request->file('images');
                foreach($images as $image) {
                    $fileEntity = new Files();
                    $fileEntity->type = 'image';
                    $fileEntity->magazine_id = $request->magazine;
                    $imageName = 'Lampiran'.$request->title.time().'Image.'.$image->getClientOriginalName();
                    $imageImage = Image::make($image->path());
                    if(!File::isDirectory($this->pathLampiran)) {
                        File::makeDirectory($this->pathLampiran, 0777, false);
                    }
                    $imageImage->resize($this->dimenLampiran, $this->dimenLampiran, function($constraint) {
                        $constraint->aspectRatio();
                    })->save($this->pathLampiran.'/'.$imageName);
                    $fileEntity->name = $imageName;
                    $fileEntity->save();
                    $fileEntity->Topic()->attach([$topic->id]);
                }
            }
            if($request->hasFile('videos') && $request->file('videos') != null) {
                $videos = $request->file('videos');
                foreach($videos as $video) {
                    $fileEntity = new Files();
                    $fileEntity->type = 'video';
                    $fileEntity->magazine_id = $request->magazine;
                    $videoName = 'Lampiran'.$request->title.time().'Video.'.$video->getClientOriginalName();
                    $video->move($this->pathLampiran, $videoName);
                    $fileEntity->name = $videoName;
                    $fileEntity->save();
                    $fileEntity->Topic()->attach([$topic->id]);
                }
            }
            return $this->onSuccess($this->dataType, $topic, 'Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function search(Request $request)
    {
        if($request->has('title') || $request->has('category')) {
            $page = 10;
            if($request->has('paginate') && $request->paginate != null) {
                $page = $request->paginate;
            }
            $topic = Topic::query();
            if($request->title != '') {
                $data = $topic->where('name', 'LIKE', '%'.$request->name.'%')->paginate($page);
            }
            if($request->category != '') {
                $data = $topic->where('category_id', $request->name)->paginate($page);
            }
            return $this->onSuccess($this->dataType, $data);
        }
    }

    public function show($id)
    {
        $topic = Topic::with('Magazine', 'File')->find($id);
        return $this->onSuccess($this->dataType, $topic);
    }

    public function edit(Topic $topic)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $topic = Topic::with('File')->find($id);
            $topic->magazine_id = $request->magazine;
            $topic->title = $request->title;
            $topic->page = $request->page;
            $topic->description = $request->description;
            if($request->category == 'Lainnya') {
                $category = new CategoryTopic();
                $category->name = $request->otherCategory;
                $category->save();
                $topic->category_id = $category->id;
            } else {
                $topic->category_id = $request->category;
            }
            $coverFile = $request->file('cover');
            if($request->hasFile('cover') && $request->file('cover') != null) {
                $coverImg = Image::make($coverFile->path());
                $this->validate($request, [
                    'cover' => 'image|mimes:jpg,png,jpeg',
                ]);
                if(!File::isDirectory($this->pathCover)) {
                    File::makeDirectory($this->pathCover);
                }
                if(!File::exists($this->pathCover.'/'.$topic->cover)) {
                    unlink($this->pathCover.'/'.$topic->cover);
                }
                $coverName = 'coverTopic_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$coverFile->extension();
                $coverImg->resize($this->dimenCover, $this->dimenCover, function($constraint) {
                    $constraint->aspectRatio();
                })->save($this->pathCover.'/'.$coverName);
                $topic->cover = $coverName;
            }
            if($request->hasFile('filePdf') && $request->file('filePdf') != null) {
                $this->validate($request, [
                    'filePdf' => 'mimes:pdf|nullable',
                ]);
                if(!File::isDirectory($this->pathPdf)) {
                    File::makeDirectory($this->pathPdf);
                }
                if(!File::exists($this->pathPdf.'/'.$topic->file_pdf)) {
                    unlink($this->pathPdf.'/'.$topic->file_pdf);
                }
                $file = $request->file('filePdf');
                $fileName = 'pdfFile_'.str_replace(' ', '_', $request->title).'_'.time().'.'.$file->extension();
                $file->move($this->pathPdf, $fileName);
                $topic->file_pdf = $fileName;
            }
            $topic->save();
            if($request->hasFile('images') && $request->file('images') != null) {
                $images = $request->file('images');
                foreach($images as $image) {
                    $fileEntity = new Files();
                    $fileEntity->type = 'image';
                    $fileEntity->magazine_id = $request->magazine;
                    $imageName = 'Lampiran'.$request->title.time().'Image.'.$image->getClientOriginalName();
                    $imageImage = Image::make($image->path());
                    if(!File::isDirectory($this->pathLampiran)) {
                        File::makeDirectory($this->pathLampiran, 0777, false);
                    }
                    $imageImage->resize($this->dimenLampiran, $this->dimenLampiran, function($constraint) {
                        $constraint->aspectRatio();
                    })->save($this->pathLampiran.'/'.$imageName);
                    $fileEntity->name = $imageName;
                    $fileEntity->save();
                    $fileEntity->Topic()->attach([$topic->id]);
                }
            }
            if($request->hasFile('videos') && $request->file('videos') != null) {
                $videos = $request->file('videos');
                foreach($videos as $video) {
                    $fileEntity = new Files();
                    $fileEntity->type = 'video';
                    $fileEntity->magazine_id = $request->magazine;
                    $videoName = 'Lampiran'.$request->title.time().'Video.'.$video->getClientOriginalName();
                    $video->move($this->pathLampiran, $videoName);
                    $fileEntity->name = $videoName;
                    $fileEntity->save();
                    $fileEntity->Topic()->attach([$topic->id]);
                }
            }
            return $this->onSuccess($this->dataType, $topic, 'Updated');
        } catch (\Exception $e) {
            // if($request->hasFile('cover')) {
            //     if(!File::exists($this->pathCover.'/'.$coverName)) {
            //         unlink($this->pathCover.'/'.$coverName);
            //     }
            // }
            return $this->onError($e);
        }
    }

    public function deleted()
    {
        $topic = Topic::whereNotNull('deleted_at')->get();
        return $this->onSuccess($this->dataType, $topic, 'Founded');
    }

    public function recover($id)
    {
        $topic = Topic::find($id);
        $topic->deleted_at = null;
        $topic->save();
        return $this->onSuccess($this->dataType, $topic, 'Recovered');
    }

    public function destroy($id)
    {
        try {
            $topic = Topic::with('File')->find($id);
            if(!File::exists($this->pathCover.'/'.$topic->cover) && $topic->cover != 'avatar.png') {
                unlink($this->pathCover.'/'.$topic->cover);
            }
            if(!File::exists($this->pathPdf.'/'.$topic->file_pdf) && $topic->file_pdf != 'dummy.pdf') {
                unlink($this->pathPdf.'/'.$topic->file_pdf);
            }
            $files = $topic->file;
            if(count($files) > 0) {
                foreach($files as $file) {
                    $selectFile = Files::find($file->id);
                    if(!File::exists($this->pathLampiran.'/'.$selectFile->name) && $selectFile->name != 'avatar.png') {
                        unlink($this->pathLampiran.'/'.$selectFile->name);
                    }
                    $selectFile->Topic()->detach();
                    $selectFile->delete();
                }
            }
            $topic->delete();
            return $this->onSuccess($this->dataType, $files, 'Destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function softDestroy($id)
    {
        try {
            $topic = Topic::find($id);
            $topic->deleted_at = Carbon::now()->toDateTimeString();
            $topic->save();
            return $this->onSuccess($this->dataType, $topic, 'Destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function deleteFiles($id)
    {
        try {
            $file = Files::find($id);
            if(!File::exists($this->pathLampiran.'/'.$file->name) && $file->name != 'avatar.png') {
                unlink($this->pathLampiran.'/'.$file->name);
            }
            $file->Topic()->detach();
            $file->delete();
            return $this->onSuccess($this->dataType, $file, 'Removed');
        } catch(\Exception $e) {
            return $this->onError($e);
        }
    }
}
