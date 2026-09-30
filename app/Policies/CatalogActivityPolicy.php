<?php

namespace App\Policies;

use App\Models\CatalogActivity;
use App\Models\User;

class CatalogActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperadmin() || $user->isCatalogAdmin();
    }

    public function view(User $user, CatalogActivity $activity): bool
    {
        return $user->isSuperadmin() || ($user->isCatalogAdmin() && $user->catalog_id === $activity->catalog_id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperadmin() || $user->isCatalogAdmin();
    }

    public function update(User $user, CatalogActivity $activity): bool
    {
        return $this->view($user, $activity);
    }

    public function delete(User $user, CatalogActivity $activity): bool
    {
        return $this->view($user, $activity);
    }
}