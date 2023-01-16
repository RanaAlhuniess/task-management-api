<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\Api\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $tasks = Task::with('categories')->latest()->filter(request(['search']))->paginate()->withQueryString();
        return TaskResource::collection($tasks);
    }

    /* Display the specified resource.
    *
    * @param  \App\Task  $task
    * @return \Illuminate\Http\Response
    */

    public function show(Task $task)
    {
        $task->load(["categories", "users", "subTasks"]);
        return $this->respond(new TaskResource($task));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|max:255',
            'description' => 'required|min:40',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails())
            return $this->respondError($validator->errors(), 422);
        $categories = $request->categories;
        $users = $request->members;
        $sub_tasks = $request->sub_tasks;

        $task_categories = !empty($categories) ? $categories : null;
        $task_users = !empty($users) ? $users : null;
        $sub_tasks = !empty($sub_tasks) ? $sub_tasks : null;

        try {
            $task = Task::create_task([
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => date_format(date_create($request->due_date), 'Y-m-d')
            ], $task_users, $task_categories, $sub_tasks);
            return $this->respond(new TaskResource($task),201);
        } catch (Exception $e) {
            $message = 'Oops! Unable to create a new Task.';
            var_dump($e->getMessage());
            return $this->respondError($message, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task    $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|max:255',
            'description' => 'required|min:40',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails())
            return $this->respondError($validator->errors(), 422);

        try {
            $task_categories = $request->categories;
            $users = $request->members;
            $sub_tasks = $request->sub_tasks;

            $task->title       = $request->title;
            $task->description = $request->description;
            $task->due_date = date_format(date_create($request->due_date), 'Y-m-d');
            $task->updated_by = auth()->id();
            $task = Task::update_task($task, $users, $task_categories, $sub_tasks);

            return $this->respond(new TaskResource($task));
        } catch (Exception $e) {
            $message = 'Oops, Failed to update the Task.';
            return $this->respondError($message, 500);
        }
    }
}
