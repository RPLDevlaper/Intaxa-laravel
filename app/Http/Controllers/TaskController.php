<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $task = Task::with('User')->get();
        return $this->onSuccess('Tasks', $task, 'Founded');
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function setStatus(Request $request, $id)
    {
        $task = Task::find($id);
        $task->status = $request->stat;
        $task->save();
        return $this->onSuccess('Tasks', $task, 'Status Changed');
    }

    public function store(Request $request)
    {
        try {
            $task = new Task();
            $task->title = $request->title;
            $task->type = $request->type;
            $task->due_date = $request->due_date;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->save();
            return $this->onSuccess('Tasks', $task, 'Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::with('User')->find($id);
        return $this->onSuccess('Task', $task, 'Founded');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::find($id);
            $task->title = $request->title;
            $task->type = $request->type;
            $task->due_date = $request->due_date;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->save();
            return $this->onSuccess('Tasks', $task, 'Created');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            $task->delete();
            return $this->onSuccess('Task', null, 'Deleted');
        } catch (\Exception $e) {
            return $this->onError($e);
        }
    }
}
