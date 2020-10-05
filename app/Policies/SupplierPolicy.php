<?php

namespace App\Policies;

use App\Supplier;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Store;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Store $store)
    {
        return true;
    }  
}
