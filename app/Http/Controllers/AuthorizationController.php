<?php
namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthorizationController extends Controller
{
    /**
     * Verify the specified permission.
     *
     * @param  string  $action
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $action)
    {
        $action = urldecode($action);
        if(!Str::contains($action, ':'))
        {
            abort(403, "Invalid format action");
        }

        list($ability, $role) = explode(':', $action);
        $data = [
            'user_role' => $request->user()->role,
            'ability' => $ability,
            'check_role' => $role,
            'permit' => Gate::allows($ability, $role)
        ];
        return $this->responseSuccess($data);
    }

    /**
     * List all permission of user.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $abilities = ['create', 'update', 'destroy'];
        $roles = ['admin', 'manager', 'scrum_master', 'developer'];
        $permissions = [];
        foreach ($abilities as $ability)
        {
            foreach ($roles as $role)
            {
                $permissions["{$ability}:{$role}"] = Gate::allows($ability, $role);
            }
        }
        $data = [
            'user_role' => $request->user()->role,
            'permissions' => $permissions
        ];
        return $this->responseSuccess($data);
    }
}