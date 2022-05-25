<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Category;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    public function getCat(Category $category)
    {
        return $category->category;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Category $category)
    {


    $category->users()->attach($request->user()->id,[],false);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category,User $user)

    {

        $request->validate([


            'rank' => ['required','numeric'],

         ]);

        foreach($category->users as $usere)
        {

          if($usere->pivot->rank==$request->rank)
          return response(["errors" => ["rank"=>"the rank has already been taken"]], 400);

        }

       $rank= $category->users()->where('id','=',$user->id)->first()->pivot->rank;
       $points=$user->points;
       if($rank!=null){
       if($rank==1)
         $points=$user->points-10;
         else
         if($rank==2)
         $points=$user->points-5;
         else
         if($rank==3)
         $points=$user->points-2;
         else
         $points=$user->points-1;
       }

         if($request->rank==1)
         $Newpoints=$points+10;
         else
         if($request->rank==2)
         $Newpoints=$points+5;
         else
         if($request->rank==3)
         $Newpoints=$points+2;
         else
         $Newpoints=$points+1;

         DB::beginTransaction();
            try{
                  $user->forceFill(['points'=>$Newpoints])->save();
                $category->users()->updateExistingPivot($user,['rank'=>$request->rank],true);
                DB::commit();
            }
       catch(Exception $e){
        DB::rollback();
       }



        }
        public function removeRank(Category $category,User $user)

        {
            $rank= $category->users()->where('id','=',$user->id)->first()->pivot->rank;
            if($rank==1)
            $points=$user->points-10;
            else
            if($rank==2)
            $points=$user->points-5;
            else
            if($rank==3)
            $points=$user->points-2;
            else
            $points=$user->points-1;

            $user->forceFill(['points'=>$points])->save();
            $category->users()->updateExistingPivot($user,['rank'=>null],true);

            }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
