<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     */
    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
   
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'task_category');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function subTasks()
    {
    	return $this->hasMany(Task::class);
    }

    //helper that creates tasks and add either categories or specific users to the task_users table
    public static function create_task($task, $users_assgin = null, $categories = null, $sub_tasks= null)
    {

        $task = self::create($task);

        if ($users_assgin) {
            $task->assign($users_assgin);
        }
        if ($categories) {
            $task->add_categories($categories);
        }
        if ($sub_tasks) {
            $collection = collect($sub_tasks)->map(function ($name) {
                return new SubTask(['description'=> $name]);
            })->reject(function ($name) {
                return empty($name);
            });
            $task->add_sub_tasks($collection);
        }
        return $task;
    }
    /**
     * users may subscribe(follow) to a current task
     * @return [type] [description]
     */
    public function assign($userIds = null)
    {
        $this->users()->attach($userIds);
        return $this;
    }


    /**
     * Unsubscribe a user from the current task.
     *
     * @param int|null $userId
     */
    public function unassign($userId = null)
    {
        $this->users()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();

        return $this;
    }

     /**
     * @return [type] [description]
     */
    public function add_categories($categoryIds = null)
    {
        $this->categories()->attach($categoryIds);
        return $this;
    }

    public function add_sub_tasks($sub_tasks) {
        $this->subTasks()->saveMany($sub_tasks);
        return $this;
    }
}
