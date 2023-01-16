<?php

use App\Mail\NotifyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('email-test', function(){
    try {
        $details['email'] = 'rana.hanis93@gmail.com';
//        $task = \App\Models\Task::find(18);
//        $email = new NotifyMail($task);
//        Mail::to($details['email'])->send($email);
        dispatch(new App\Jobs\SendEmailJob($details))->delay(now()->addMinutes(10));;
    }catch (\Exception $e) {
        dd($e->getMessage());
    }


    dd('done');
});
