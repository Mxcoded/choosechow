<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function view(User $user, Menu $menu)
    {
        return $user->id === $menu->chef_id;
    }

    public function create(User $user)
    {
        return $user->user_type === 'chef';
    }

    public function update(User $user, Menu $menu)
    {
        return $user->id === $menu->chef_id;
    }

    public function delete(User $user, Menu $menu)
    {
        return $user->id === $menu->chef_id;
    }
}
