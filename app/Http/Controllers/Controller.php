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

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class Controller extends BaseController
{
    /**
     * Success
     *
     * @param array $data Data array
     * @param int   $code Status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, $code)
    {
        return response()->json(['data' => $data], $code);
    }

    /**
     * Error
     *
     * @param string $message Error message
     * @param int    $code    Status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message, $code)
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Function validate incoming request
     *
     * @param \Illuminate\Http\Request $request incoming request
     * @param array                    $rules   validations rules
     *
     * @return void
     */
    public function validateRequest(Request $request, $rules)
    {
        $this->validate($request, $rules);
    }
}
