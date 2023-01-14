<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\Api\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(Task::with('categories')->paginate());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categories = json_decode($request->input('categories'));
        $users = json_decode($request->input('members'));
        $sub_tasks = json_decode($request->input('sub_tasks'));

        $task_categories = !empty($categories) ? $categories : null;
        $task_users = !empty($users) ? $users : null;
        $sub_tasks = !empty($sub_tasks) ? $sub_tasks : null;

        DB::beginTransaction();
        try {
            $task = Task::create_task([
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'due_date' => $request->input('due_date'),
            ], $task_users, $task_categories, $sub_tasks);
            DB::commit();
            return $this->respond(new TaskResource($task));
        } catch (Exception $e) {
            DB::rollBack();
            $message = 'Oops! Unable to create a new Task.';
            return $this->respondError($message, 500);
        }
    }
}
