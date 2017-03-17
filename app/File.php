<?php
/**
 * File Model
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

use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 *
 * @category Interview
 * @package  App
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'deleted', 'version'
    ];

    // protected $table = 'user_files';

    /**
     * Define relation with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_file');
    }
}
