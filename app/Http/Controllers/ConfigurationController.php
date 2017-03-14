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

use App\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ConfigurationController
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class ConfigurationController extends Controller
{
    /**
     * Function return user configuration
     *
     * @param integer $id user id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $loggedUser = Auth::user();
        if ($id != $loggedUser->id) {
            return $this->error("You can view only yours configuration", 404);
        }

        $configuration = Configuration::where('user_id', $id)->first();
        if ($configuration) {
            return $this->success($configuration, 200);
        }

        return $this->error("Can't find configuration for this user", 404);
    }

    /**
     * Function update user configuration
     *
     * @param Request $request input request
     * @param integer $id      user id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $loggedUser = Auth::user();
        if ($id != $loggedUser->id) {
            return $this->error("You can update only yours configuration", 404);
        }

        $configuration = Configuration::where('user_id', $id)->first();

        if (!$configuration) {
            return $this->error("Can't find configuration for this user", 404);
        }

        $rules = [
            'user_id'         => 'numeric|unique:users',
            'version_history' => 'boolean',
            'token_time'      => 'numeric',
        ];

        $this->validateRequest($request, $rules);

        $configuration->version_history
            = ($request->get('version_history') !== null)
            ? $request->get('version_history')
            : $configuration->version_history;
        $configuration->token_time = $request->get('token_time')
            ? $request->get('token_time') : $configuration->token_time;
        $configuration->save();
        $message
            = "Configuration for user with id $loggedUser->id has been updated";

        return $this->success(['data' => $message,], 200);

    }

    /**
     * Function create user configuration
     *
     * @param Request $request input object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($request)
    {
        $loggedUser = Auth::user();
        $rules = [
            'user_id'         => 'required|unique:users',
            'version_history' => 'required|boolean',
            'token_time'      => 'required|numeric',
        ];

        $this->validateRequest($request, $rules);

        $configuration = Configuration::create(
            [
                'user_id'         => $loggedUser->id,
                'version_history' => $request->get('version_history'),
                'token_time'      => $request->get('token_time'),]
        );
        $message
            = "Configuration for user with id $loggedUser->id has been created";

        if ($configuration) {
            return $this->success(['data' => $message,], 201);
        }
        $this->error("Something go wrong", 404);
    }
}
