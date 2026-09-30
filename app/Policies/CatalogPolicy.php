<?php

namespace App\Policies;

use App\Models\Catalog;
use App\Models\User;

class CatalogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperadmin() || $user->isCatalogAdmin();
    }

    public function view(User $user, Catalog $catalog): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isCatalogAdmin() && $user->catalog_id === $catalog->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    public function update(User $user, Catalog $catalog): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isCatalogAdmin() && $user->catalog_id === $catalog->id;
    }

    public function delete(User $user, Catalog $catalog): bool
    {
        return $user->isSuperadmin();
    }

    public function publish(User $user, Catalog $catalog): bool
    {
        return $user->isSuperadmin();
    }

    public function manageAdmins(User $user): bool
    {
        return $user->isSuperadmin();
    }

    public function manageCategories(User $user): bool
    {
        return $user->isSuperadmin();
    }
}
