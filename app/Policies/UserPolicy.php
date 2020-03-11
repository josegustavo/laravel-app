<?php

namespace App\Policies;

use App\User;

class UserPolicy
{

    protected $rules = [
        'admin' => ['manager'],
        'manager' => ['scrum_master'],
        'scrum_master' => ['developer'],
        'developer' => [],
    ];

    /**
     * Determine if a given user has permission to create, update or delete
     *
     * @param User $currentUser
     * @param String $role
     * @return bool
     */
    public function create(User $currentUser, $role)
    {
        $user_role = $currentUser->getAttribute('role');
        return array_key_exists($user_role, $this->rules) && in_array($role, $this->rules[$user_role]);
    }

    /**
     * Determine if a given user has permission to update
     *
     * @param User $currentUser
     * @param String $role
     * @return bool
     */
    public function update(User $currentUser, $role)
    {
        $user_role = $currentUser->getAttribute('role');
        return array_key_exists($user_role, $this->rules) && in_array($role, $this->rules[$user_role]);
    }

    /**
     * Determine if a given user has permission to delete
     *
     * @param User $currentUser
     * @param String $role
     * @return bool
     */
    public function destroy(User $currentUser, $role)
    {
        $user_role = $currentUser->getAttribute('role');
        return array_key_exists($user_role, $this->rules) && in_array($role, $this->rules[$user_role]);
    }

}