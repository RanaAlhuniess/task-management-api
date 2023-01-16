<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\DB;

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
        return $this->hasMany(SubTask::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
            )
        );
    }

    //helper that creates tasks and add either categories or specific users to the task_users table
    public static function create_task($task, $users_assgin = null, $categories = null, $sub_tasks = null)
    {
        DB::beginTransaction();
        try {
            $task = self::create($task);

            if ($users_assgin) {
                $task->assign($users_assgin);
            }
            if ($categories) {
                $task->add_categories($categories);
            }
            if ($sub_tasks) {
                $collection = collect($sub_tasks)->map(function ($name) {
                    return new SubTask(['description' => $name]);
                })->reject(function ($name) {
                    return empty($name);
                });
                $task->add_sub_tasks($collection);
            }

            DB::commit();
            return $task;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public static function update_task($task, array $users_assgin, $categories = null, $sub_tasks = null)
    {
        DB::beginTransaction();
        try {
            $task->save();

            if ($users_assgin) {
                $task->users()->sync($users_assgin);
            }

            if ($categories) {
                $task->categories()->sync($categories);
            }

            $task->subTasks()->delete();

            if ($sub_tasks) {
                //TODO: 
                $collection = collect($sub_tasks)->map(function ($name) {
                    return new SubTask(['description' => $name]);
                })->reject(function ($name) {
                    return empty($name);
                });
                $task->add_sub_tasks($collection);
            }
            DB::commit();
            return $task;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
    private function add_categories($categoryIds = null)
    {
        $this->categories()->attach($categoryIds);
        return $this;
    }

    private function add_sub_tasks($sub_tasks)
    {
        $this->subTasks()->saveMany($sub_tasks);
        return $this;
    }
}
