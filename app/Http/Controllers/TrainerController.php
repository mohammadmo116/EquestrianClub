<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Room;

class TrainerController extends Controller
{

    use PasswordValidationRules;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $a=[];
         $i=0;
      $allTrainers=Trainer::all();
      foreach($allTrainers as $trainer)
        {
            $likes=[];
            $disLikes=[];
            $j=0;
            $k=0;
            foreach($trainer->users as $user)
            {
                if($user->pivot->like=='1')
                {$likes[$j]=$user->username;
                $j++;}
                else if($user->pivot->like=='0')
                {$disLikes[$k]=$user->username;
                $k++;}
            }
            $a[$i]=['trainer'=> $trainer,
                    'likesCount'=>$trainer->users()->wherePivot('like','=','1')->count(),
                    'dislikesCount'=>$trainer->users()->wherePivot('like','=','0')->count(),
                    'likes'=>$likes,
                    'disLikes'=>$disLikes,
        ];
        $i++;    }

      return  $a;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([

            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Admin::class),
                Rule::unique(Trainer::class),
                Rule::unique(User::class),
            ],
            'username' => ['required', 'string', 'max:255',
             Rule::unique(User::class),
              Rule::unique(Trainer::class),
              Rule::unique(Admin::class)],

              'phone' => [ 'string', 'max:20'],
              'password' => $this->passwordRules(),


         ]);

        Room::create([
            'FirstUserUsername'=>$request->post('username'),
            'SecondUserUsername'=>Admin::first()->username,

        ]);

        return Trainer::create([
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'username' => $request->post('username'),
            'phone' => $request->post('phone'),
            'password' => Hash::make($request->post('password')),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Trainer $trainer)
    {
        return $trainer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Trainer $trainer)
    {

        $request->validate([

            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Admin::class),
                Rule::unique(Trainer::class)->ignore($trainer->id),
                Rule::unique(User::class),
            ],
            'username' => ['required', 'string', 'max:255',
             Rule::unique(User::class),
              Rule::unique(Trainer::class)->ignore($trainer->id),
              Rule::unique(Admin::class)],

              'phone' => [ 'string', 'max:20'],



         ]);

         $trainer->update([
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'username' => $request->post('username'),
            'phone' => $request->post('phone'),
         ]);
    }


    public function setLike(Request $request,Trainer $trainer)
    {
        $user=User::findOrFail($request->user()->id);

        if($trainer->users()->where('id','=',$request->user()->id)->first()==null)
        {
             $user->trainers()->attach($trainer,['like'=>'1'],true);}
        else
        {
        $like=$trainer->users()->where('id','=',$request->user()->id)->first()->pivot->like;
        if($like==1)
        $user->trainers()->detach($trainer);

        else if($like==0)
        $user->trainers()->updateExistingPivot($trainer,['like'=>'1'],true);



         }
             }
    public function setDisLike(Request $request,Trainer $trainer)
    {
        $user=User::findOrFail($request->user()->id);

        if($trainer->users()->where('id','=',$request->user()->id)->first()==null)
        {
             $user->trainers()->attach($trainer,['like'=>'0'],true);}
        else
        {
        $like=$trainer->users()->where('id','=',$request->user()->id)->first()->pivot->like;
        if($like==0)
        $user->trainers()->detach($trainer);

        else if($like==1)
        $user->trainers()->updateExistingPivot($trainer,['like'=>'0'],true);



         }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trainer $trainer)
    {
       $room= Room::where('FirstUserUsername','=',$trainer->username)->Where('SecondUserUsername','=',Admin::first()->username)
        ->orWhere('FirstUserUsername','=',Admin::first()->username)->Where('SecondUserUsername','=',$trainer->username)->first();
        if($room)
        $room->deleteOrFail();
        $trainer->deleteOrFail();

    }


}
