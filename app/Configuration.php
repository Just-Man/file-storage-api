<?php
/**
 * Configuration model
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
 * Configuration model
 *
 * Php version 5.6 || 7.0
 *
 * @category Interview
 * @package  App\Models
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class Configuration extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'version_history', 'token_time'
    ];

    protected $table = 'user_configuration';
}
