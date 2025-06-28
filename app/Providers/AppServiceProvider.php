<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\DB;



class AppServiceProvider extends ServiceProvider
{
   
    public function register(): void{

    
        $this->app->singleton(LoginResponseContract::class, LoginResponse ::class);
    
    }

    
    

    public function boot()
        {
            if (env('DB_TIMEZONE')) {
                DB::statement("SET time_zone='".env('DB_TIMEZONE')."'");
            }
        }
}
