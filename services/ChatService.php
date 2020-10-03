<?php namespace Jcc\Im\Services;

use Jcc\Im\Http\Resources\Collect\ChatRecordCollection;
use Jcc\Im\Models\ChatRecord;

class ChatService extends AbstractChatService
{


    //群员信息
    public function members($data)
    {
        // TODO: Implement members() method.
    }

    //聊天记录
    public function chatRecords($data)
    {

        $type = $data['type'];
        $user = auth('api')->user();
        $records = collect([]);
        switch ($type) {
            case 'friend':
                $records = ChatRecord::whereIn(
                    'send_id',
                    [$user->id, $data['model_id']]
                )
                    ->whereIn(
                        'receive_id',
                        [$user->id, $data['model_id']]
                    )->where('type', ChatRecord::TYPE_FRIEND)->oldest('created_at')->paginate(request()->limit ?? 10);
                break;
            case 'group':
                //todo 可根据加入时间过滤掉加入之前的信息
                $records = ChatRecord::where('receive_id', 0)
                    ->where('group_id', $data['model_id'])
                    ->where('type', ChatRecord::MSG_TYPE_GROUP)->oldest('created_at')->paginate(request()->limit ?? 10);
                break;
        }

        return new ChatRecordCollection($records);
    }

}
