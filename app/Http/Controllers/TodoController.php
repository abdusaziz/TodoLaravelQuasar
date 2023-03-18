<?php

namespace App\Http\Controllers;

use App\Http\Requests\todoStoreRequest;
use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Resources\TodoResource;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TodoResource::collection(
            Todo::where('user_id',Auth::user()->id)
                    ->where('status','0')
                    ->get()
        );
    }

    public function complete(){
        return TodoResource::collection(
            Todo::where('user_id',Auth::user()->id)
                    ->where('status','1')
                    ->get()
        );
    }

    public function store(todoStoreRequest $request)
    {
        $request->validated($request->all());

        $todo = Todo::create([
            "user_id"   =>  Auth::user()->id,
            "title"      =>  $request->title,
            "status"      =>  $request->status
        ]);

        return new TodoResource($todo);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        if (Auth::user()->id !== $todo->user_id) {
            return $this->error("","You are not Authorized to make this request.",403);
        }
        $todo->update($request->all());
        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        return $this->isNotAuthorised($todo) ? $this->isNotAuthorised($todo) : $todo->delete();
    }

    private function isNotAuthorised($todo){
        if (Auth::user()->id !== $todo->user_id) {
            return $this->error("","You are not Authorized to make this request.",403);
        }
    }

}
