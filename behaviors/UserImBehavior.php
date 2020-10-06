<?php namespace Jcc\Im\Behaviors;

use Jcc\Im\Models\ChatRecord;
use Jcc\Im\Models\MsgBox;

class UserImBehavior extends \October\Rain\Extension\ExtensionBase
{
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function sayHello()
    {
        echo "Hello from " . get_class($this->parent);
    }

    public function getNoReadChatRecord()
    {
        $noReadChatRecords = $this->getNoReadChatRecordIds();

        if (!empty($noReadChatRecords)) {
            $records = ChatRecord::where(function ($query) {
                $query->where('receive_id', $this->parent->id)->where('if_read', ChatRecord::IF_READ_0);
            })->orWhere(function ($query) use ($noReadChatRecords) {
                $query->whereIn('id', $noReadChatRecords);
            })->get();
        } else {
            $records = ChatRecord::where(function ($query) {
                $query->where('receive_id', $this->parent->id)->where('if_read', ChatRecord::IF_READ_0);
            })->get();
        }
        return $records;
    }

    public function getNoReadChatRecordIds()
    {
        $noReadChatRecordKey = 'User:' . $this->parent->id . 'ChatRecord:NoRead';//存放未读的消息id
        $chatRecordIds       = [];
        if (\Cache::has($noReadChatRecordKey)) {
            $chatRecordIds = json_decode(\Cache::get($noReadChatRecordKey), true);
        }
        return $chatRecordIds;
    }

    public function setNoReadChatRecordIds($record)
    {
        $key         = 'User:' . $this->parent->id . 'ChatRecord:NoRead';
        $noRecordIds = [];
        if (\Cache::has($key)) {
            $noRecordIds = json_decode(\Cache::get($key), true);
        }
        $noRecordIds[] = $record->id;

        if (count($noRecordIds) > 100) {//存放一百条信息
            $noRecordIds = array_slice($noRecordIds, -100);
            //todo 根据用户的最近一次登陆时间，是否是僵尸用户，就不记了
        }

        \Cache::put($key, json_encode($noRecordIds), now()->addWeeks(1));//存放未读用户
    }


    public function clearNoReadChatRecord()
    {
        $noReadChatRecords = $this->getNoReadChatRecordIds();
        if (!empty($noReadChatRecords)) {
            ChatRecord::where(function ($query) {
                $query->where('receive_id', $this->parent->id)->where('if_read', ChatRecord::IF_READ_0);
            })->orWhere(function ($query) use ($noReadChatRecords) {
                $query->whereIn('id', $noReadChatRecords);
            })->update(['if_read' => ChatRecord::IF_READ_1]);
        } else {
            ChatRecord::where(function ($query) {
                $query->where('receive_id', $this->parent->id)->where('if_read', ChatRecord::IF_READ_0);
            })->update(['if_read' => ChatRecord::IF_READ_1]);
        }
        $noReadChatRecordKey = 'User:' . $this->parent->id . 'ChatRecord:NoRead';
        \Cache::forget($noReadChatRecordKey);
    }

    public function getNoReadMsgboxs()
    {
        return $this->parent->msgboxes()->where('read', MsgBox::READ_0)->get();
    }

    public function setNoReadMsgboxesReaded()
    {
        return $this->parent->msgboxes()->where('read', MsgBox::READ_0)->update(['read' => MsgBox::READ_1]);
    }

    public function saveCharRecord($data)
    {
        $record                   = new ChatRecord();
        $record->type             = $data['type'];
        $record->chat_source_type = $data['chat_source_type'];//后台追加
        $record->content_type     = $data['content_type'];
        $record->from_id          = $this->parent->id;
        $record->from_avatar      = $this->parent->avatar->path;
        $record->content          = $data['content']['value'];
        $record->extra            = $data['content'];
        $record->save();
        return $record;
    }

    //绑定到wbsocket的唯一标识符

    public function getBindImId()
    {
        return $this->parent->id;
    }

}
