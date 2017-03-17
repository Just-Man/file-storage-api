<?php

/**
 * User Model
 *
 * Php version 5.6 || 7.0
 *
 * @category Interview
 * @package  App\Models
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Class User
 *
 * @category Interview
 * @package  App
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Define relation with File
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany('App\File', 'user_file');
    }
}
