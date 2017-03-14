<?php

namespace App\Providers;

use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest(
            'api',
            function ($request) {
                if ($request->input('token')) {
                    $token = $request->get('token');
                    $tokenArray = json_decode($token, true);

                    $verifiedToken = $this->_verifyToken($tokenArray[0][1]);
                    if (!$verifiedToken) {
                        throw new Exception('Token is not verified');
                    }

                    $is_validated = $this->_validateToken($verifiedToken);
                    if (!$is_validated) {
                        throw new Exception('Token is expired or not valid');
                    }
                    $id = $is_validated['id'];
                    return User::find($id);
                }
            }
        );
    }

    /**
     * Verify token
     *
     * @param string $token Token
     *
     * @return string $parsedToken
     */
    private function _verifyToken($token)
    {
        $signer = new Sha256();
        $key = env('TOKEN_KEY');
        $parsedToken = (new Parser())->parse((string)$token);
        $isVerify = $parsedToken->verify($signer, $key);
        if ($isVerify) {
            return $parsedToken;
        }

        return '';
    }
    /**
     * Validate token
     *
     * @param string $token Token
     *
     * @return mixed
     */
    private function _validateToken($token)
    {
        $data = new ValidationData();
        $isValid = $token->validate($data);

        if ($isValid) {
            $user = json_encode($token->getClaims());
            $user = json_decode($user, true);

            $loggedUser = [
                'id'   => $user['user_id'],
                'name' => $user['name']
            ];

            return $loggedUser;
        }

        return false;
    }
}
