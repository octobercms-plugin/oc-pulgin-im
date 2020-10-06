<?php

namespace Jcc\Im\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jcc\Jwt\Models\User;
use Jcc\Im\Models\ChatRecord;

class ChatRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function chatRecords(User $user, $data)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        $type    = $data['type'];
        $modelId = $data['model_id'];

        if ($type == 'friend') {
            if (ChatRecord::where('from_id', $user->id)->where('to_user_id', $modelId)->exists()) {
                return true;
            }
            return false;
        } elseif ($type == 'group') {
            if (ChatRecord::where('from_id', $user->id)->where('to_group_id', $modelId)->exists()) {
                return true;
            }
            return false;
        }
        return false;
    }
}
