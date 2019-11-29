<?php

namespace App\Models;

use Gwaps4nlp\Core\Models\User as Gwaps4nlpUser;

class User extends Gwaps4nlpUser
{
    /**
    * Check if the user has a role
    *
    * @param Role $role
    * @return boolean
    */
    public function hasRole($role): bool
    {
        return $this->roles->contains('id', $role->id);
    }
}