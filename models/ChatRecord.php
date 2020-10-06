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
        'extra' => 'json'
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
            'key' => 'front_id'
        ],
        'to_user'   => [
            \Jcc\Jwt\Models\User::class,
            'key' => 'to_id'
        ],
        'to_group'  => [
            Group::class,
            'key' => 'to_group_id'
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
    const CHAT_SOURCE_TYPE_FRIEND = 'friend';
    const CHAT_SOURCE_TYPE_GROUP = 'group';
    const CHAT_SOURCE_TYPE_FRIEND_SYSTEM = 'friend-system'; //推送消息的时候知道是好友里的系统消息分开两个
    const CHAT_SOURCE_TYPE_FRIEND_SYSTEM_NOT_ONLINE = 'friend-system-not-online'; //推送消息的时候知道是好友里的系统消息分开两个
    const CHAT_SOURCE_TYPE_GROUP_SYSTEM = 'group-system';
    public static $chatSourceTypeMaps = [
        self::CHAT_SOURCE_TYPE_FRIEND                   => '朋友消息',//朋友之前互相发送的消息
        self::CHAT_SOURCE_TYPE_GROUP                    => '群组消息',//在群里有用户发送的消息
        self::CHAT_SOURCE_TYPE_FRIEND_SYSTEM            => '好友系统消息', //在好友聊天里发达的系统消息
        self::CHAT_SOURCE_TYPE_FRIEND_SYSTEM_NOT_ONLINE => '对方不在线...',
        self::CHAT_SOURCE_TYPE_GROUP_SYSTEM             => '群系统消息',//在群里发送的系统消息

    ];


    //发送的消息内容类型
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


    const SYSTEM_TYPE_NOT_ONLINE = 'not_online';

    public static $systemTypeMaps = [
        self::SYSTEM_TYPE_NOT_ONLINE => '对方已离线...'
    ];


    /**
     * 发送消息时候的消息结构
     * @param $user
     * @param $data
     * @param string $type friend group friend-system group-system
     * @return mixed
     */
    public static function transform_msg($user, $data)
    {
        $msg = [
            'type'             => $data['type'],
            'from_id'          => $user->id,
            'from_avatar'      => $user->avatar->path,
            'to_id'            => $data['model_id'],
            'chat_source_type' => $data['chat_source_type'],//后台追加的
            'content_type'     => $data['content_type'],
            'content'          => $data['content']['value'],
            'extra'            => $data['content'] //额外的一些信息
        ];
        return $msg;
    }

    public static function user_not_online_send_system_key($id)
    {
        return 'user_not_online_send_system:' . $id;
    }


}
