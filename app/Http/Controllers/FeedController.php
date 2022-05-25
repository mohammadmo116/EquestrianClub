<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return Feed::OrderBy('created_at','DESC')->Paginate(6);
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
            'title' => 'required','string',
            'post' => 'required','string',
            'image' => 'nullable','file|max:2500',

         ]);

         $feed=Feed::create([
            'title'=>$request->post('title'),
            'post'=>$request->post('post'),

        ]);

        if($request->hasFile('image')){

            $file=$request->file('image');

            if($file->isValid()){
               Storage::deleteDirectory('feeds/'.$feed->id);
                $path=$file->storeAs('feeds/'.$feed->id,$feed->id."_feed.".$file->getClientOriginalExtension(),['disk'=>'public']);

                $feed->forceFill([
                    'image'=> $path
                ])->save();




            }
            else{
                return Response::json([
                    'File'=>'file corrupted',
                ] );


            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function show(Feed $feed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feed $feed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
       $feed->deleteOrFail();
    }
}
