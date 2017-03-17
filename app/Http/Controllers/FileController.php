<?php
/**
 * Created by PhpStorm.
 * User: just
 * Date: 12.03.17
 * Time: 05:59
 *
 * Php version 5.6 || 7.0
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class FileController
 *
 * @category Interview
 * @package  App\Http\Controllers
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class FileController extends Controller
{
    /**
     * Function display all files attached to account
     *
     * @param int $accountId User account id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($accountId)
    {
        $loggedUser = Auth::user();
        if ($accountId != $loggedUser->id) {
            return $this->error(
                "You can'\t view only yours files",
                404
            );
        }
        $files = $this->getUserFiles($accountId);

        return $this->success($files, 200);
    }

    /**
     * Save file in storage
     *
     * @param \Illuminate\Http\Request $request input request object
     *
     * @return \Illuminate\Http\JsonResponse response object
     */
    public function upload(Request $request)
    {
        $loggedUser = Auth::user();
        $files = $request->allFiles();

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $userPath = $this->_userPath($loggedUser->id);

            $version = $this->_versionCheck($fileName, $loggedUser, $userPath);

            $is_moved = $file->move($userPath, $fileName);
            if ($is_moved) {
                $file = $this->_storeToDatabase($fileName, $version);
                $fileId = $file->getAttribute('id');
                $loggedUser->files()->attach($fileId);
            }
        }
        $message = 'success';

        return $this->success(['data' => $message,], 200);
    }

    /**
     * Return file with current file name
     *
     * @param string $name file name
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($name)
    {
        $user = Auth::user();
        $userFiles = $this->getUserFiles($user->id);

        foreach ($userFiles as $userFile) {
            if ($name == $userFile->name) {

                return $this->success($userFile, 200);

            }
        }

        return $this->error('We can\'t find this file name', 404);
    }

    /**
     * Delete file with current file name
     *
     * @param string $name file name
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($name)
    {
        $loggedUser = Auth::user();
        $userPath = $this->_userPath($loggedUser->id);
        $this->_versionCheck($name, $loggedUser, $userPath);
        File::update('deleted', true)->where('name', $name);
        $message = 'success';

        return $this->success(['data' => $message,], 200);
    }

    /**
     *  Function create user director
     *
     * @param string $userPath User directory path
     * @param int    $version  File version
     *
     * @return boolean $success
     */
    public function createUserDirectory($userPath, $version = null)
    {
        if (!file_exists($userPath)) {
            $success = mkdir($userPath, 0775, true);
        }
        if ($version !== false) {
            $versionPath = $userPath . '/version_' . ($version + 1);
            if (!file_exists($versionPath)) {
                $success = mkdir($versionPath, 0775, true);
            }
        }
    }

    /**
     *  Function get all user files
     *
     * @param int $id User id
     *
     * @return array $files Array with user files
     */
    protected function getUserFiles($id)
    {
        $basePath = env('FILE_STORAGE_PATH');
        $files = scandir($basePath . '/' . $id);

        return $files;
    }

    /**
     * Function store file to Database
     *
     * @param string $fileName File name
     * @param int    $version  File version
     *
     * @return integer $fileId file id
     */
    private function _storeToDatabase($fileName, $version)
    {
        $fileId = File::create(
            [
                'name'    => $fileName,
                'deleted' => 0,
                'version' => $version,
            ]
        );

        return $fileId;
    }

    /**
     * Create user folder path
     *
     * @param integer $userId User id
     *
     * @return string $userPath user folder path
     */
    private function _userPath($userId)
    {
        $basePath = env('FILE_STORAGE_PATH');
        $userPath = $basePath . '/' . $userId . '/';

        return $userPath;
    }

    /**
     * Check are file is already stored
     *
     * @param Auth::user() $loggedUser User
     * @param string       $filename   File name
     *
     * @return integer $version  File version
     */
    private function _checkIsFileExist($loggedUser, $filename)
    {
        $exist = $loggedUser->files()->where('name', $filename)->first();

        return $exist;
    }

    /**
     * Move old file to version directory
     *
     * @param string  $userPath Base user directory
     * @param integer $version  File version
     * @param string  $fileName File name
     *
     * @return void
     */
    private function _moveFileToVersionFolder($userPath, $version, $fileName)
    {
        if ($version == false) {
            $currentFolder = $userPath;
        } else {
            $previewsVersion = $version;
            $currentFolder = $userPath . 'version_' . $previewsVersion . '/';
        }
        $newFolder = $userPath . 'version_' . ($version + 1) . '/';
        if (copy($currentFolder . $fileName, $newFolder . $fileName)) {
            unlink($currentFolder . $fileName);
        }
    }

    /**
     * Check file version
     *
     * @param string $fileName   Filename
     * @param object $loggedUser Logged user
     * @param string $userPath   User path
     *
     * @return integer
     */
    private function _versionCheck($fileName, $loggedUser, $userPath)
    {
        $exist = $this->_checkIsFileExist($loggedUser, $fileName);
        $version = $exist ? $exist->version : false;
        if ($version !== false) {
            $this->createUserDirectory($userPath, $version);
            $this->_moveFileToVersionFolder($userPath, $version, $fileName);
        }

        return $version;
    }
}
