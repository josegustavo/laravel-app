<?php
namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with('projects:projects.id')->get();
        return $this->responseSuccess($users);
    }

    /**
     * Store and validate newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        Gate::authorize('create', $request->get('role'));

        $users = User::create($request->all());

        return $this->responseSuccess($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return $this->responseSuccess($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::with('projects:projects.id,name,code')->findOrFail($id);

        Gate::authorize('update', $user->role);

        $new_projects = $request->get('projects');
        if(!isset($new_projects))
        {
            abort(401, 'Project list needed.');
        }

        $projects = Project::find($new_projects);
        $user->projects()->detach();
        $user->projects()->saveMany($projects);

        $user = User::with('projects:projects.id,name,code')->findOrFail($id);
        return $this->responseSuccess($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        Gate::authorize('destroy', $user->role);

        $user->delete();

        return $this->responseSuccess();
    }
}