<?php

namespace App\Http\Controllers;

use App\Models\CategoryTopic;
use Illuminate\Http\Request;

class CategoryTopicController extends Controller
{

    public $ctr;
    public $dataType;

    public function __construct(CategoryTopic $ctr)
    {
        $this->ctr = $ctr;
        $this->dataType = 'Category Topic';
    }

    public function index(Request $request)
    {
        if($request->has('paginate')) {
            $page = $request->paginate;
            $categoryTopic = $this->ctr->paginate($page);
            return $this->onSuccess($this->dataType, $categoryTopic);
        }
        $categoryTopic = $this->ctr->all();
        return $this->onSuccess($this->dataType, $categoryTopic);
    }

    public function create()
    {
        //
    }

    public function search(Request $request)
    {
        if($request->has('name')) {
            $page = 10;
            if($request->has('paginate') && $request->paginate != null) {
                $page = $request->paginate;
            }
            $categoryTopic = $this->ctr->where('name', 'LIKE', '%'.$request->name.'%')->paginate($page);
            return $this->onSuccess($this->dataType, $categoryTopic);
        }
    }

    public function store(Request $request)
    {
        try {
            $categoryTopic = $this->ctr->create($request->only('name'));
            return $this->onSuccess($this->dataType, $categoryTopic, 'Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function show($id)
    {
        $categoryTopic = $this->ctr->find($id);
        return $this->onSuccess($this->dataType, $categoryTopic);
    }

    public function edit(CategoryTopic $categoryTopic)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $categoryTopic = $this->ctr->find($id)->update($request->only(['name']));
            $category = $this->ctr->find($id);
            return $this->onSuccess($this->dataType, $category, 'Updated');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $categoryTopic = $this->ctr->destroy($id);
            return $this->onSuccess($this->dataType, null, 'destroyed');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }
}
