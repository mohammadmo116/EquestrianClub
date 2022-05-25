<?php

namespace App\Http\Controllers;

use App\Events\NewTournamentEvent;
use Pusher\Pusher;
use App\Models\User;
use App\Models\Category;
use App\Models\Tournament;
use App\Notifications\DeleteTournamentNotification;
use App\Notifications\EditTournamentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Notifications\NewTournamentNotification;
use Illuminate\Support\Facades\Date;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Tournament::OrderBy('date','DESC')->Paginate(3);
    }
    public function index2()
    {
        return Tournament::OrderBy('date','DESC')->get();
    }



    public function threeT()
    {

        return Tournament::limit(3)->OrderBy('date','DESC')->get();

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
            'club'=>['required', 'string', 'max:255'],
            'size'=>['required','numeric'],
            'location'=>['required', 'string', 'max:255'],
            'description'=>['required', 'string', 'max:1000'],
            'date'=>['required','date','after:today'],
            'private'=>['nullable'],
            'email' => ['required','string','email','max:255'],
            'category'=>['required'],
            'image' => ['nullable','mimes:jpeg,png,jpg,svg','max:2500'],

         ]);


        $cat=$request->post('category');
         for($i=0;$i<sizeof($cat);$i++)
        $array[$i]=['category' => $cat[$i]];

       $t= Tournament::create([
          'name' => $request->post('name'),
          'club' => $request->post('club'),
          'email' => $request->post('email'),
          'size' => $request->post('size'),
          'location' => $request->post('location'),
          'description' => $request->post('description'),
          'date' => $request->post('date'),
          'private' => $request->post('private')=='true'?true:false,
        ]);
        $t->categories()->createMany($array);

        if($request->hasFile('image')){

            $file=$request->file('image');

            if($file->isValid()){
               Storage::deleteDirectory('t/'.$t->id);
                $path=$file->storeAs('t/'.$t->id,$t->name."_t.".$file->getClientOriginalExtension(),['disk'=>'public']);

                $t->forceFill([
                    'image'=> $path
                ])->save();

            }
            else{
                return Response::json([
                    'File'=>'file corrupted',
                ] );


            }

        }

foreach(User::all() as $user)
          { $user->notify(new NewTournamentNotification($t));
            event(new NewTournamentEvent('new tournament has been added',$user->username));

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function show(Tournament $tournament)
    {
        return $tournament->with('categories')->get()->where('id','=',$tournament->id)->first();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tournament $tournament)
    {
        $request->validate([


            'name' => ['required', 'string', 'max:255'],
            'club'=>['required', 'string', 'max:255'],
            'size'=>['required','numeric'],
            'location'=>['required', 'string', 'max:255'],
            'description'=>['required', 'string', 'max:1000'],
            'date'=>['required','date','after:today'],
            'private'=>['nullable'],
            'email' => ['required','string','email','max:255'],
            'category'=>['required'],
            'image' => ['nullable','mimes:jpeg,png,jpg,svg','max:2500'],
         ]);


         $cat=$request->post('category');
         for($i=0;$i<sizeof($cat);$i++)
        $array[$i]=['category' => $cat[$i]];

        $tournament->forceFill([
            'name' => $request->post('name'),
            'club' => $request->post('club'),
            'email' => $request->post('email'),
            'size' => $request->post('size'),
            'location' => $request->post('location'),
            'description' => $request->post('description'),
            'date' => $request->post('date'),
            'private' => $request->post('private')=='true'?true:false,
        ])->save();

        if($request->hasFile('image')){

            $file=$request->file('image');

            if($file->isValid()){
               Storage::deleteDirectory('t/'.$tournament->id);
                $path=$file->storeAs('t/'.$tournament->id,$tournament->name."_t.".$file->getClientOriginalExtension(),['disk'=>'public']);

                $tournament->forceFill([
                    'image'=> $path
                ])->save();

            }
            else{
                return Response::json([
                    'File'=>'file corrupted',
                ] );


            }

        }
              $tournament->categories()->delete();
             $tournament->categories()->createMany($array);
             foreach(User::all() as $user)
             { $user->notify(new EditTournamentNotification($tournament));
               event(new NewTournamentEvent('tournament has been Modified',$user->username));

           }
    }


    public function userSrearch(Request $request)
    {
         $t= Tournament::OrderBy('date','DESC');

       if($request->get('name'))
       {
        $t=$t->where('name', 'LIKE', "%{$request->get('name')}%");
       }
       if($request->get('location'))
       {
        $t=$t->where('location', 'LIKE', "%{$request->get('location')}%");
       }
       if($request->get('size'))
       {$size=$request->get('size');
           if($size=='Small')
        $t=$t->where('size', '<', '60');
        if($size=='Medium')
        $t=$t->whereBetween('size', ['60','100']);
        if($size=='Large')
        $t=$t->where('size', '>', '100');
       }




            if($request->get('from')&&$request->get('to')){
                $from=date('Y-m-d', strtotime($request->get('from')));
                $to=date('Y-m-d', strtotime($request->get('to')));
                $t->whereBetween('date', [$from,$to]);
            }
            else{
       if($request->get('from'))
       {
        $from=date('Y-m-d', strtotime($request->get('from')));
        $t=$t->where('date', '>=',$from);
       }
        if($request->get('to'))
       {
        $to=date('Y-m-d', strtotime($request->get('to')));
        $t=$t->where('date', '<=',$to);
       }
    }
        return $t->Paginate(3);

    }
    public function userTournaments(Request $request)
    {
       return $request->user()->categories()->with('tournaments')->get();

    }
    public function tournamentsGhart()
    {
        $data=[];
        $now=date('Y-m-d', strtotime('  + 1 months'));

        $plus=0;


       for($i=11;$i>=0;$i--){
        $end= date('Y-m-d', strtotime($now. '  - '.$plus.' months'));
        $plus++;
        $from=date('Y-m-d', strtotime(date('Y-m', strtotime($end))));
        $to=date('Y-m-d', strtotime(date('Y-m', strtotime($end)). '  + 30 day'));
        $endd=strtotime($end);

        $data[$i]=[
              'name'=>date("M", $endd),
             '#Tournaments'=>Tournament::whereBetween('date', [$from,$to])->get()->count(),
        ];


       }

         return $data;


       return Tournament::all();

    }
    public function getC(Tournament $tournament)
    {

    return $tournament->categories()->orderBy('category')->get();;
    }
    public function getP(Tournament $tournament)
    {
        $count=0;
        $i=0;
        $a=[];
       $categories= $tournament->categories()->orderBy('category', 'DESC')->get();

        foreach($categories as $category)
        {
            if($category->users()->count()>$count)
            $count=$category->users()->count();
            foreach($category->users as $user)
{
            $a[$i]=[
                'id'=>$i,
                'user'=>$user,
               'category'=>$category->category,
               'count'=>$count
            ];
            $i++;}
        }


     return $a;


    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tournament $tournament)
    {
         $tournament->deleteOrFail();
        foreach(User::all() as $user)
        { $user->notify(new DeleteTournamentNotification($tournament));
          event(new NewTournamentEvent('tournament has been Deleted',$user->username));

      }
    }
}
