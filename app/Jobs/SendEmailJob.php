<?php

namespace App\Jobs;

use App\Mail\NotifyMail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->task->mark_as_ended();
            $users = $this->task->users;
            $emails = array();
            foreach ($users as $user) {
                $emails[] = $user['email'];
            }
            if (!empty($emails)) {
                $email = new NotifyMail($this->task);
                Mail::to('rana.hanis93@gmail.com')->send($email);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }
}
