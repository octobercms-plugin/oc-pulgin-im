<?php

namespace Jcc\Im\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;

class ImRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //todo 非好友不能发私信 做个开关
        //todo 非群友不能发信息 做个开关
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $method = $this->route()->getActionMethod();
        switch ($method) {

            case 'bind':
                $rules = [
                    'client_id' => ['required'],
                ];
                break;
            case 'send':
                $rules = [
                    'type'      => ['required', Rule::in(['friend', 'group'])], // 群消息还是好友友消息
                    'friend_id' => ['required_if:friend'],//todo uuid //朋友唯一标识符
                    'group_id'  => ['required_if:group'],//todo uuid //朋友唯一标识符
                    'content'   => [
                        'required',
                        'array',
                        function ($attribute, $value, $fail) {
                            //todo 信息有效性进行验证
                            //todo type: text emoji file system img
                        }
                    ]
                ];
                break;
            default:
                break;

        }
        return $rules;
    }
}
