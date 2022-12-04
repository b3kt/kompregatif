<?php

namespace App\Http\Controllers\Voyager;

use TCG\Voyager\Http\Controllers\VoyagerAuthController as BaseVoyagerAuthController;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User;
use App\Models\User as AppUser;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class VoyagerAuthController extends BaseVoyagerAuthController
{

    public function postLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        $appUser = AppUser::whereEmail($credentials['email'])->first();
        if(!empty($appUser)){
            if ($this->guard()->attempt($credentials, $request->has('remember'))) {
                return $this->sendLoginResponse($request);
            }
        }else{
            if($this->ldapAuthenticate($credentials)){
                if ($this->guard()->attempt($credentials, $request->has('remember'))) {
                    return $this->sendLoginResponse($request);
                }
            }else{
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.ldap.failed')],
                ]);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    private function ldapAuthenticate($credentials){

        $connection = Container::getConnection('default');
        try{
            $user = User::findByOrFail('sAMAccountName', $credentials['email']);
            if ($connection->auth()->attempt($user->getDn(), $credentials['password'])) {
                // CHECK USER IF DOESNT EXIST, CREATE!
                $this->createUserIfNotExists($credentials);
                return true;
            }
        }catch(Exception $ex){
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.ldap.user_not_found')],
            ]);
        }
        return false;
    }

    /**
     * Create user if not
     */
    private function createUserIfNotExists($credentials){
        $appUser = AppUser::whereEmail($credentials['email'])->first();

        if(empty($appUser)){
            $createdUser = AppUser::create(array(
                'name'=> $credentials['email'],
                'email'=> $credentials['email'],
                'password'=> bcrypt($credentials['password']),
                'remember_token' => Str::random(10),
            ));
        }
    }
}
