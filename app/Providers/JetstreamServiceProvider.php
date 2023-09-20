<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\Auth\UserCompany;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Fortify;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'company_id'=>'required|exists:companies,id',
                'year'=>'required|exists:years,year',
            ]);

            $user = User::where('email', $request->email)->first();
             //FOR INFOWAY
            if($user->id != 1){
                $user_companies = $user->user_companies->pluck('company_id')->toArray();
                $request->validate([
                    'company_id'=>'required|in:'.implode(",", $user_companies),
                ]);
            }

            if($user->user_branches && count($user->user_branches)== 1){
                session()->put('branch_id', $user->user_branches[0]->branch_id);
            }


            if ($user &&
                Hash::check($request->password, $user->password)) {
                session()->put('company_id', $request->company_id);
                session()->put('fy', $request->year);
                return $user;
            }
        });

    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            return Inertia::render('Auth/Login',[
                'companies' => getCompany(),
                'years' =>getYears()
            ]);
        });

        $this->configurePermissions();
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
