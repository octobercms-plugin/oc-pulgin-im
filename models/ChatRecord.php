<?php namespace Jcc\Im\Models;

use Model;

/**
 * ChatRecord Model
 */
class ChatRecord extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'jcc_im_chat_records';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['if_read'];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'content' => 'json'
    ];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'from_user' => [
            \Jcc\Jwt\Models\User::class,
            'key' => 'send_id'
        ],
        'to_user'   => [
            \Jcc\Jwt\Models\User::class,
            'key' => 'receive_id'
        ],
        'group'     => [
            Group::class,
            'key' => 'group_id'
        ],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    const IF_READ_0 = 0;
    const IF_READ_1 = 1;

    public static $readMaps = [
        self::IF_READ_0 => '未读',
        self::IF_READ_1 => '已读',
    ];

    const TYPE_GROUP = 'group';
    const TYPE_FRIEND = 'friend';


}
