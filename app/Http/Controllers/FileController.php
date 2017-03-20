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

        if ($accountId != $this->userId) {
            return $this->error(
                "You can'\t view only yours files",
                404
            );
        }
        $files = $this->getUserFiles($this->user);

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

        if (!count($files)) {
            return $this->error('You must choose file to upload', 403);
        }
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $isValidFileName = $this->_validateFileName($fileName);
            if (count($isValidFileName['error'])) {
                return $this->error(implode(',', $isValidFileName['error']), 400);
            }

            $exist = $this->_versionCheck($fileName, $loggedUser, $this->userPath);

            $is_moved = $file->move($this->userPath, $fileName);
            $version = $exist ? $exist->version : false;

            if ($is_moved && $version === false) {
                $file = $this->_storeToDatabase($fileName, $version);
                $fileId = $file->getAttribute('id');
                $loggedUser->files()->attach($fileId);
            }
        }
        $message = 'success';

        return $this->success(['Upload' => $message,], 200);
    }

    /**
     * Return file with current file name
     *
     * @param integer $id   user id
     * @param string  $name file name
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, $name)
    {
        if ($id != $this->userId) {
            return $this->error("You can view only yours configuration", 404);
        }
        $userFiles = $this->getUserFiles($this->user, $name);

        if ($userFiles) {
            return $this->success(['file' => $userFiles], 201);
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
        $file = $this->_versionCheck($name, $this->user, $this->userPath);
        $file->deleted = true;
        $file->update();
        $message = 'success';

        return $this->success(['data' => $message,], 200);
    }

    /**
     * Validate File name
     *
     * @param string $fileName File name
     *
     * @return array
     */
    private function _validateFileName($fileName)
    {
        $isValid['error'] = false;
        $size = mb_strlen($fileName, "UTF-8");
        if ($size > 1024) {
            $isValid['error'][] = 'Max allowed size is 1024';
        }
        $denied = [
            "\\\\","\\\^","\\\`","\\\>","\\\<","\\\{","\\\}","\]","\[","\\\#","\\\%",
        "\~","\,"
        ];

        foreach ($denied as $item) {
            $pass = preg_match_all("!$item!", $fileName, $matches);
            if (!$pass) {
                $isValid['error'][] = sprintf("you can't use %s", substr($item, -1));
            }
        }

        return $isValid;
    }

    /**
     *  Function create user director
     *
     * @param string $userPath User directory path
     * @param int    $version  File version
     *
     * @return void
     */
    public function createUserDirectory($userPath, $version = null)
    {
        if (!file_exists($userPath)) {
            mkdir($userPath, 0775, true);
        }
        if ($version !== false) {
            $versionPath = $userPath . '/version_' . ($version + 1);
            if (!file_exists($versionPath)) {
                mkdir($versionPath, 0775, true);
            }
        }
    }

    /**
     *  Function get user file/s
     *
     * @param object $loggedUser User object
     * @param string $filename   File name
     *
     * @return array $files Array with user files
     */
    protected function getUserFiles($loggedUser, $filename = '')
    {
        if ($filename) {
            $file = $this->_checkIsFileExist($loggedUser, $filename);
            $file = $file->toArray();
            $filePath = $this->userPath . $file['name'];
            $content_type = mime_content_type($filePath);

            header("Content-Type: \"$content_type\"");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Pragma: no-cache');
            readfile($filePath);
            exit;

        } else {
            $files = $loggedUser->files()->where('deleted', false)->get();
            $files = $files->toArray();
            foreach ($files as $key => $file) {
                unset($file['pivot']);
                $files[$key] = $file;
            }
        }

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
     * Check are file is already stored
     *
     * @param Auth::user() $loggedUser User
     * @param string       $filename   File name
     *
     * @return object $exist  File
     */
    private function _checkIsFileExist($loggedUser, $filename)
    {
        $exist = $loggedUser->files()->where(
            ['name' => $filename,
             'deleted' => false]
        )->first();

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
            $currentFolder = $userPath . 'version_' . $version . '/';
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
     * @return object
     */
    private function _versionCheck($fileName, $loggedUser, $userPath)
    {
        $exist = $this->_checkIsFileExist($loggedUser, $fileName);
        $version = $exist ? $exist->version : false;
        if ($version !== false) {
            $this->createUserDirectory($userPath, $version);
            $this->_moveFileToVersionFolder($userPath, $version, $fileName);
            $exist->updated_at = time();
            $exist->update();
        }

        return $exist;
    }
}
