<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperadmin() || $user->isCatalogAdmin();
    }

    public function view(User $user, Product $product): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isCatalogAdmin() && $user->catalog_id === $product->catalog_id;
    }

    public function create(User $user): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isCatalogAdmin() && $user->catalog?->type === 'umkm';
    }

    public function update(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }
}
