<?php
/**
 * Php version 5.6 || 7.0
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * Class AccountController
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class AccountController extends Controller
{

    /**
     * Function return all users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::all();

        return $this->success(['data' => $users], 200);
    }

    /**
     * Create new user account.
     *
     * @param Request $request Request array
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $rules = [
            'name'     => 'required|string|min:3',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];

        $this->validateRequest($request, $rules);

        $user = User::create(
            [
                'name'     => $request->get('name'),
                'email'    => $request->get('email'),
                'password' => password_hash(
                    $request->get('password'),
                    PASSWORD_BCRYPT
                ),
            ]
        );

        $configuration = new ConfigurationController();
        $configuration->create($request, $user->id);

        $fileStorage = new FileController();
        $fileStorage->createUserDirectory($user->name);

        return response()->json(
            ['data' => "The user with with id {$user->id} has been created"],
            201
        );
    }

    /**
     * Login function
     *
     * @param Request $request input request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->error('Email doesn\'t match', 400);
        }

        if (!password_verify($password, $user->password)) {
            return $this->error('Password doesn\'t match', 400);
        }

        $token = $this->_createUserToken($user);

        $data = [
            'token' => token_get_all($token),
            'user'  => [
                "name" => $user['name'],
            ],
            'error' => false,
        ];

        return $this->success($data, 200);
    }

    /**
     * Function show user by id
     *
     * @param User $id User id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if ($id != $this->userId) {
            return $this->error("You can view only your account", 404);
        }

        if (!$this->user) {
            return $this->error("The user with {$id} doesn't exist", 404);
        }

        return $this->success(['user' => $this->user], 200);
    }

    /**
     * Function for update user by id
     *
     * @param \Illuminate\Http\Request $request incoming request
     * @param User                     $id      User id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return $this->error("The user with {$id} doesn't exist", 404);
        }

        $rules = [
            'name'     => 'string|min:3',
            'email'    => 'email',
            'password' => 'min:6',
        ];

        $this->validateRequest($request, $rules);
        $user->name = $request->get('name') ? $request->get('name')
            : $user->name;
        $user->email = $request->get('email') ? $request->get('email')
            : $user->email;
        $user->password = $request->get('password') ? password_hash(
            $request->get('password'), PASSWORD_BCRYPT
        ) : $user->password;

        $user->save();

        return $this->success(
            ['data' => "The user with with id {$user->id} has been updated"],
            200
        );
    }

    /**
     * Function delete user by id
     *
     * @param User $id User id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $user = User::find($id);

        if (!$user) {
            return $this->success(
                ['message' => "The user with {$id} doesn't exist"], 404
            );
        }

        $user->delete();

        return $this->success(
            ['data' => "The user with with id {$id} has been deleted"], 200
        );
    }

    /**
     * Function create user token
     *
     * @param User $user User object
     *
     * @return \Lcobucci\JWT\Token
     */
    private function _createUserToken($user)
    {
        $configuration = new ConfigurationController();
        $userConfiguration = $configuration->getUserConfigurations($user->id);

        $signer = new Sha256();
        $token = (new Builder())
            ->setIssuer("Api")
            ->setAudience("Client Side")
            ->setIssuedAt(time())
            ->setExpiration(time() + $userConfiguration->token_time)
            ->set('user_id', $user->id)
            ->set('name', $user->name)
            ->sign($signer, env('TOKEN_KEY'))
            ->getToken();

        return $token;
    }
}
