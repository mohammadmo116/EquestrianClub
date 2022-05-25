<?php

use App\Models\Horse;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HorseController;

use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TokenAuthController;
use App\Http\Controllers\pusherAuthController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProfilePictureController;
use App\Http\Controllers\RoomController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$limiter = config('fortify.limiters.login');

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/login', [TokenAuthController::class, 'store'])->name('login')->middleware(array_filter([
    'guest:'.config('fortify.guard'),
    $limiter ? 'throttle:'.$limiter : null,
]));
Route::delete('/loagout', [TokenAuthController::class, 'destroy'])->name('loagout')->middleware('auth:sanctum');
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/recoverCodes', [ProfilePictureController::class, 'codeEmail']);
Route::get('/search', [TournamentController::class, 'userSrearch']);


Route::middleware([config('fortify.guard'),'auth:sanctum'])->prefix(config('fortify.prefixApi'))->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/profilePic', [ProfilePictureController::class,'store']);
    Route::get('/profilePic', [ProfilePictureController::class,'show']);
    Route::get('/getNotifications', [NotificationController::class,'index']);
    Route::get('/getNotification/{id}', [NotificationController::class,'show']);
    Route::get('/getUserHorses', [HorseController::class,'getUserHorses']);
    Route::get('/getUserCourses', [ScheduleController::class,'getUserCourses']);
    Route::get('/getUserRooms', [RoomController::class,'getRooms']);
    Route::get('/getMessages/{room:id}', [MessageController::class,'getMessages']);
    Route::post('/sendMessage/{room:id}', [MessageController::class,'store']);

});

Route::get('/tournamentsP', [TournamentController::class,'index']);
Route::get('/tournaments', [TournamentController::class,'index2']);
Route::get('/threeT', [TournamentController::class,'threeT']);
Route::get('/getPoints', [ProfilePictureController::class,'getPoints']);
Route::get('/posts', [FeedController::class,'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/getCourses', [ScheduleController::class,'index']);
    Route::get('/getUserCourses', [ScheduleController::class,'getUC']);
    Route::get('/trainers', [TrainerController::class,'index']);
    Route::get('/getTrainerCourses/{trainer:id}', [ScheduleController::class,'getTC']);
    Route::get('/courses/{schedule:id}/payments', [PaymentController::class,'create']);
    Route::get('/courses/{schedule:id}/payments/return', [PaymentController::class,'callback'])
    ->name('payments.callback');
    Route::post('/pusher/userAuth', [pusherAuthController::class,'index']);
    Route::get('/getUserTournaments', [TournamentController::class,'userTournaments']);
    Route::get('/getCats/{tournament:id}', [TournamentController::class,'getC']);
    Route::put('/addUser/{category:id}', [CategoryController::class,'store']);
    Route::put('/getCat/{category:id}', [CategoryController::class,'getCat']);
    Route::post('/like/{trainer:id}', [TrainerController::class,'setLike']);
    Route::post('/disLike/{trainer:id}', [TrainerController::class,'setDisLike']);


});

Route::middleware(['admin','auth:sanctum'])->prefix('admin')->group(function () {
    Route::post('/register', [TrainerController::class,'store']);
    Route::get('/trainers/{trainer:id}', [TrainerController::class,'show']);
    Route::put('/editTrainer/{trainer:id}', [TrainerController::class,'update']);
    Route::delete('/trainers/{trainer:id}', [TrainerController::class,'destroy']);
    Route::delete('/tournaments/{tournament:id}',[TournamentController::class,'destroy']);
    Route::post('/addHorse',[HorseController::class,'store']);
    Route::get('/horses',[HorseController::class,'index']);
    Route::get('/horses/{horse:id}',[HorseController::class,'show']);
    Route::delete('/horses/{horse:id}',[HorseController::class,'destroy']);
    Route::put('/Horses/{horse:id}',[HorseController::class,'update']);
    Route::get('/getP/{tournament:id}',[TournamentController::class,'getP']);
    Route::put('/setRank/{category:id}/{user:id}',[CategoryController::class,'update']);
    Route::put('/removeRank/{category:id}/{user:id}',[CategoryController::class,'removeRank']);
    Route::get('/getAllTrainerCourses/{trainer:id}', [ScheduleController::class,'getAllTC']);
    Route::get('/getCounts', [ProfilePictureController::class,'TUCount']);
    Route::get('/tournamentsGhart', [TournamentController::class,'tournamentsGhart']);
    Route::get('/coursesCount', [ScheduleController::class,'coursesCount']);
    Route::get('/incomeSum', [ScheduleController::class,'incomeSum']);

    Route::post('/addPost', [FeedController::class,'store']);
    Route::delete('/deletePost/{feed:id}', [FeedController::class,'destroy']);
    Route::post('/addTournament', [TournamentController::class,'store']);
    Route::post('/editTournaments/{tournament:id}',[TournamentController::class,'update']);
    Route::put('/editPrice/{price:id}',[PriceController::class,'update']);


});
Route::middleware(['trainer','auth:sanctum'])->prefix('trainer')->group(function () {
    Route::post('/addCourse', [ScheduleController::class,'store']);
    Route::put('/editCourse/{schedule:id}', [ScheduleController::class,'update']);
    Route::delete('/removeCourse/{schedule:id}', [ScheduleController::class,'destroy']);
    Route::get('/getRCourses', [ScheduleController::class,'getRTC']);
    Route::get('/getAllTC', [ScheduleController::class,'getAllTCmy']);


});

Route::middleware(['AT','auth:sanctum'])->group(function () {
    Route::get('/tournaments/{tournament:id}',[TournamentController::class,'show']);
    Route::get('/user/{user:id}', [ProfilePictureController::class,'getUser']);

});






// Route::get('/testdata', [testControler::class, 'index'])->middleware(['auth:sanctum'])->name('testdata');





