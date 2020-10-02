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

    // 好友聊天框 群聊天框
    const TYPE_GROUP = 'group';
    const TYPE_FRIEND = 'friend';
    public static $typeMaps = [
        self::TYPE_GROUP  => '群消息',
        self::TYPE_FRIEND => '朋友消息',//不限于好友
    ];

    // 好友聊天框( 用户 和 系统) 群聊天框（群友和系统）
    const MSG_TYPE_FRIEND = 'friend';
    const MSG_TYPE_GROUP = 'group';
    const MSG_TYPE_FRIEND_SYSTEM = 'friend-system'; //推送消息的时候知道是好友里的系统消息分开两个
    const MSG_TYPE_GROUP_SYSTEM = 'group-system';
    public static $msgTypeMaps = [
        self::MSG_TYPE_FRIEND        => '朋友消息',//朋友之前互相发送的消息
        self::MSG_TYPE_GROUP         => '群组消息',//在群里有用户发送的消息
        self::MSG_TYPE_FRIEND_SYSTEM => '好友系统消息', //在好友聊天里发达的系统消息
        self::MSG_TYPE_GROUP_SYSTEM  => '群系统消息',//在群里发送的系统消息
    ];


    //发送的消息类型
    const CONTENT_TYPE_TEXT = 'text'; //文本消息
    const CONTENT_TYPE_IMG = 'img'; //图片消息
    const CONTENT_TYPE_FILE = 'file'; //文件消息
    const CONTENT_TYPE_EMOJI = 'emoji'; //表情消息 todo 可内嵌到文本中

    public static $contentTypeMaps = [
        self::CONTENT_TYPE_TEXT  => '文本消息',
        self::CONTENT_TYPE_IMG   => '图片消息',
        self::CONTENT_TYPE_FILE  => '文件消息',
        self::CONTENT_TYPE_EMOJI => '表情消息',
    ];

    /**
     * @param $user
     * @param $data
     * @param string $type friend group friend-system group-system
     * @return mixed
     */
    public static function msg($user, $data, $type = 'friend')
    {
        $msg['username']  = $user->username;
        $msg['avatar']    = $user->avatar->path;
        $msg['id']        = $user->id;
        $msg['timestamp'] = time() * 1000;
        $msg['content']   = $data['content'];
        $msg['type']      = $type;
        return $msg;
    }

    public static function user_not_online_send_system_key($id)
    {
        return 'user_not_online_send_system:'.$id;
    }


}
