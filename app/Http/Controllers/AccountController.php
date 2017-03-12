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
        return response()->json(['data' => $users], 200);
    }

    /**
     * Create new user account.
     *
     * @param Request $request Request array
     *
     * @return Response
     */
    public function register(Request $request)
    {

        $this->validateRequest($request);

        $user = User::create(
            [
                'name'    => $request->get('name'),
                'email'    => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]
        );

        return response()->json(
            ['data' => "The user with with id {$user->id} has been created"],
            201
        );
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

        $user = User::find($id);

        if (!$user) {
            return response()->json(
                ['message' => "The user with {$id} doesn't exist"], 404
            );
        }

        return response()->json(['data' => $user], 200);
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
            return response()->json(
                ['message' => "The user with {$id} doesn't exist"], 404
            );
        }

        $this->validateRequest($request);

        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));

        $user->save();
        return response()->json(
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
            return response()->json(
                ['message' => "The user with {$id} doesn't exist"], 404
            );
        }

        $user->delete();

        return response()->json(
            ['data' => "The user with with id {$id} has been deleted"], 200
        );
    }

    /**
     * Function validate incoming request
     *
     * @param \Illuminate\Http\Request $request incoming request
     *
     * @return void
     */
    public function validateRequest(Request $request)
    {

        $rules = [
            'name'    => 'required|name',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];

        $this->validate($request, $rules);
    }
}
