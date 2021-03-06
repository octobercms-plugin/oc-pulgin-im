<?php return [
    'settings' => [
        'menu_label'       => 'Gateway 设置',
        'menu_description' => '',
        'fields'           => [
            'registerAddress'     => [
                'label' => '服务端地址',
            ],
            'support_before_bind' => [
                'label'   => '是否支持im绑定之前事件',
                'comment' => '可添加绑定的数据，以适配不同平台的绑定操作'
            ],
            'support_after_bind'  => [
                'label'   => '是否支持im绑定之后事件',
                'comment' => '在绑定完成之后，做一些推送操作，比如未读消息等'

            ],
            'group_chat_record'   => [
                'label'   => '是否记录群组聊天记录',
                'comment' => ''
            ],
            'friend_chat_record'  => [
                'label'   => '是否记录好友聊天记录',
                'comment' => ''

            ],
            'user_not_online_send_system_msg'  => [
                'label'   => '用户不在线是否发送系统离线消息',
                'comment' => ''

            ],
        ],
    ],
    'plugin'   => [
        'name'        => 'IM',
        'description' => '实时聊天',
    ]
];
