<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Helpers\IamPln\SsoAgent;

class SSOController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function test(Request $request){
        $clientId = config('sso.client_id');
        $clientSecret = config('sso.client_secret');
        $redirectUri = config('sso.redirect_uri');


        $agent = new SsoAgent(
            'https://iam.pln.co.id/svc-core/oauth2',
            'https://iam.pln.co.id/svc-core/oauth2/auth',
            'https://iam.pln.co.id/svc-core/oauth2/token',
            'https://iam.pln.co.id/svc-core/oauth2/me',
            $clientId, // <-- change this
            $clientSecret, // <-- change this
            $redirectUri, // <-- change this
            'https://iam.pln.co.id/svc-core/oauth2/logout',
            array("openid", "email", "phone", "profile", "empinfo",
            "address"),
            true,
            true
            );


        if(!$agent->isAuthenticated())
            $agent->authenticate();

        echo "Hello world!!!";
    }


    public function callback(Request $request){
        exit('test');
    }


    public function redirect()
    {
        // $user_check = User::where('username', 'auzan.muhammad')->first();
        // auth()->login($user_check, true);
        // return;
        $client_id = config('sso.client_id');
        $redirect_uri = config('sso.redirect_uri');
        $iam_url = 'https://iam.pln.co.id/svc-core/oauth2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=openid email profile empinfo phone address';

        exit(var_dump($iam_url));

        // return redirect($iam_url);
    }

    public function handle(Request $request)
    {
        if (!$request->code) {
            return redirect()->route('voyager.login')
                ->withErrors('You did not share your profile data with our app.');
        }

        // Get access token
        $client_id = config('sso.client_id');
        $client_secret = config('sso.client_secret');
        $redirect_uri = config('sso.redirect_uri');

        $url_access_token = 'https://iam.pln.co.id/svc-core/oauth2/token';
        $url_get_user = 'https://iam.pln.co.id/svc-core/oauth2/me';
        try {
            $http = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)
                ],
                'verify' => false
            ]);

            $response = $http->post($url_access_token, [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri'  => $redirect_uri,
                    'code'          => $request->code,
                ],
            ]);

            exit(var_dump($response));


            // Explode response access token
            $data = (array)json_decode((string)$response->getBody(), true);
            $access_token = $data['access_token'];
            $id_token = $data['id_token'];
            session(['sso_id_token' => $id_token]);

            $http = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'verify' => false
            ]);
            $response = $http->get($url_get_user);
            $data = json_decode((string)$response->getBody(), true);
            $user_sso = collect($data);

            // Check user in database
            if (isset($user_sso['https://iam.pln.co.id/svc-core/account/hrinfo'])) {
                $user_sso_hr = $user_sso['https://iam.pln.co.id/svc-core/account/hrinfo'];
                $user_check = User::where('nip', $user_sso_hr['nip'])->first();
            } else {
                $user_check = User::where('username', strtolower($user_sso['sub']))->first();
            }

            $user = new User;
            if ($user_check) {
                // Existing user
                $user = $user_check;
            } else {
                // New user
                $user->status = 'ACTV';
            }

            $user->fullname = ucwords(ltrim($user_sso->has('name') ? $user_sso['name'] : ''));
            $user->username = strtolower($user_sso->has('sub') ? $user_sso['sub'] : $user_sso['preferred_username']);
            $user->email = strtolower($user_sso->has('email') ? $user_sso['email'] : $user->username . '@pln.co.id');
            $user->password = bcrypt(str_random(16));
            $user->remember_token = str_random(100);
            $user->is_sso = true;
            // $user->is_active_directory  = true;

            if (isset($user_sso['https://iam.pln.co.id/svc-core/account/hrinfo'])) {
                // SSO Attributes
                if($user->is_sync== true){
                    $user_sso_hr = $user_sso['https://iam.pln.co.id/svc-core/account/hrinfo'];
                    $user->fullname = strtoupper($user_sso_hr['registeredName']);
                    $user->nip = $user_sso_hr['nip'] ?? null;
                    $user->phonenumber = $user_sso_hr['phone'] ?? null;
                    $user->title = strtoupper($user_sso_hr['posisi']['name']) ?? null;
                    $user->company = strtoupper($user_sso_hr['personnelSubArea']['name']) ?? null;
                    $user->department = strtoupper($user_sso_hr['organisasi']['name']) ?? null;
                    $user->company_code = $user_sso_hr['companyCode']['id'] ?? null;
                    $user->business_area = $user_sso_hr['businessArea']['id'] ?? null;
                    $user->personnel_area = $user_sso_hr['personnelArea']['id'] ?? null;
                    $user->personnel_sub_area = $user_sso_hr['personnelSubArea']['id'] ?? null;
                    $user->grade = $user_sso_hr['grade'] ?? null;
                    // $user->officer_nip = isset($user_sso_hr['officer']) ? explode(' - ', $user_sso_hr['officer'])[0] : null;
                    $user->pernr = $user_sso_hr['pernr'] ?? null;
                    $user->organization_code = $user_sso_hr['organisasi']['id'] ?? null;
                    $user->job_level = $user_sso_hr['jenisJabatan']['id'] ?? null;
                    $user->job_sub_level = $user_sso_hr['jenisJabatan']['id'] ?? null;
                    $user->position_code = $user_sso_hr['posisi']['id'] ?? null;
                    $user->is_external = false;
                }

            } else {
                // Not detected as employee
                if (!$user_check) {
                    $message = 'Terjadi kegagalan pada username ' . strtolower($user_sso['sub']) . ' dalam mendapatkan informasi kepegawaian. Mohon dicoba kembali atau dapat menghubungi helpdesk dengan menyertakan screnshoot error yang muncul.';
                    //ActivityLog::logWithProperty('Failed Login', 'Username ' . strtolower($user_sso['sub']) . ' terdeteksi sebagai akun non pegawai.', $user_sso);
                    return redirect()->route('auth.login.index')->withErrors($message);
                }
            }
            $user->disableLogging();
            $user->save();

            // Check if user in active
            if ($user->status != 'ACTV') {
                $message = 'Maaf, user anda tidak aktif dengan status ' . $user->status . '. Mohon menghubungi helpdesk dengan menyertakan screnshoot error yang muncul.';
                //ActivityLog::logWithProperty('Failed Login', 'Username ' . strtolower($user_sso['sub']) . ' mencoba login IAM dengan status ' . $user->status . '.', $user);
                return redirect()->route('auth.login.index')->withErrors($message);
            }

            // Check if user in active date
            // if(!$user->in_active_date) {
            //     $message = 'Maaf, user anda dalam masa non aktif. Silahkan hubungi admin untuk perpanjang waktu.';
            //     return redirect()->route('auth.login.index')->withErrors($message);
            // }

            // Check unit
            // if(!$user->organizationUnit) {
            //     $message = 'Maaf, unit organisasi ('.$user->organization_code.') - '.$user->department.' tidak ditemukan. Silahkan menghubungi helpdesk dengan menyertakan screnshoot error yang muncul.';
            //     return redirect()->route('auth.login.index')->withErrors($message);
            // }

            // if(!$user->personnelArea) {
            //     $message = 'Maaf, personnel area ('.$user->personnel_area.') - '.$user->company.' tidak ditemukan. Silahkan menghubungi helpdesk dengan menyertakan screnshoot error yang muncul.';
            //     return redirect()->route('auth.login.index')->withErrors($message);
            // }

            // if(!$user->personnelSubArea) {
            //     $message = 'Maaf, personnel sub area ('.$user->personnel_sub_area.') - '.$user->company.' tidak ditemukan. Silahkan menghubungi helpdesk dengan menyertakan screnshoot error yang muncul.';
            //     return redirect()->route('auth.login.index')->withErrors($message);
            // }

            // Assign role
            if (!count($user->getRoleNames())) {
                // Add guest role
                $user->assignRole('pegawai');
            }

            // Auth login
            auth()->login($user);

            // Check if auth correct
            if (auth()->check()) {
                // User last login at
                auth()->user()->last_login_at = Carbon::now();
                auth()->user()->last_login_ip = request()->ip();
                auth()->user()->disableLogging();
                auth()->user()->save();

                //ActivityLog::log('Login', ':causer.fullname has been login using IAM');
                return redirect()->intended(route('backend.dashboard.index'));
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            exit(var_dump($e));

            //ActivityLog::sentry($e);
            return redirect()->route('voyager.login')
                ->withErrors('Errors when redirect to IAM PLN.');
        } catch (QueryException $e) {
            //ActivityLog::sentry($e);
            return redirect()->route('voyager.login')
                ->withErrors(trans('exceptions.query'));
        } catch (\Exception $e) {
            //ActivityLog::sentry($e);
            return redirect()->route('voyager.login')
                ->withErrors(trans('exceptions.generic'));
        }
    }

    public function logout()
    {
        if (session('sso_id_token')) {
            $logout_url = config('sso.logout_uri');
            $id_token = session('sso_id_token');
            $iam_logout_url = 'https://iam.pln.co.id/svc-core/oauth2/session/end?post_logout_redirect_uri=' . $logout_url . '&id_token_hint=' . $id_token;

            return redirect($iam_logout_url);
        }
        return route('auth.logout');
    }
}
