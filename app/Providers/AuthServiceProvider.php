<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'         => 'App\Policies\ModelPolicy',
        App\Supplier::class => App\Policies\SupplierPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // //custom gate to check if user has permission to access
        // Gate::define('isAdmin', function($user){

        //     return $user->type === 'admin';
        // });

        // Gate::define('isSales', function($user){

        //     return $user->type === 'sales';
        // });

        // Gate::define('isAdminOrSales', function($user){

        //     if($user->type==='admin' || $user->type==='sales'){

        //         return true;
        //     }
        // });

        Gate::define('hasPermission', function ($user, $action) {

            $permissions = $user->roles()->first();

            $permissions = $permissions->permissions()->first()->name;

            $permissions = explode(',', $permissions); //seperate name string by ',' and push them to array

            if (in_array($action, $permissions) || in_array('all', $permissions)) {

                return true;
            } else {
                return false;
            }

        });

        Gate::define('hasStore', function ($user, $store) {

            $check = $user->stores()->where('store_id', $store);

            if ($check->first()) {

                return true;
            } else {
                return false;
            }
        });

    }
}
