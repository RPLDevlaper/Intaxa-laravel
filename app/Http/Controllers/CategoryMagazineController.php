<?php

namespace App\Http\Controllers;

use App\Models\CategoryMagazine;
use Illuminate\Http\Request;

class CategoryMagazineController extends Controller
{

    public $ctr;
    public $dataType;

    public function __construct(CategoryMagazine $ctr)
    {
        $this->ctr = $ctr;
        $this->dataType = 'Category Magazine';
    }

    public function index(Request $request)
    {
        if($request->has('paginate')) {
            $page = $request->paginate;
            $categoryMagazine = $this->ctr->paginate($page);
            return $this->onSuccess($this->dataType, $categoryMagazine);
        }
        $categoryMagazine = $this->ctr->all();
        return $this->onSuccess($this->dataType, $categoryMagazine);
    }

    public function search(Request $request)
    {
        if($request->has('name')) {
            $page = 10;
            if($request->has('paginate') && $request->paginate != null) {
                $page = $request->paginate;
            }
            $categoryMagazine = $this->ctr->where('name', 'LIKE', '%'.$request->name.'%')->paginate($page);
            return $this->onSuccess($this->dataType, $categoryMagazine);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $categoryMagazine = $this->ctr->create($request->only('name'));
            return $this->onSuccess($this->dataType, $categoryMagazine, 'Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function show($id)
    {
        $categoryMagazine = $this->ctr->find($id);
        return $this->onSuccess($this->dataType, $categoryMagazine);
    }

    public function edit(CategoryMagazine $categoryMagazine)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $categoryMagazine = $this->ctr->find($id)->update($request->only(['name']));
            $category = $this->ctr->find($id);
            return $this->onSuccess($this->dataType, $category, 'Updated');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $categoryMagazine = $this->ctr->destroy($id);
            return $this->onSuccess($this->dataType, null, 'destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }
}
