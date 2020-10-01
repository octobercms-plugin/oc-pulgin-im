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
        $method = $this->route()->getActionMethod();

        switch ($method) {

            case 'send':
                //todo 发送权限验证
                //todo 非好友不能发私信 做个开关
                //todo 非群友不能发信息 做个开关

                break;
            case 'chatRecords':
                //todo 发送权限验证
                break;
        }

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
                    'client_id' => ['required', 'string'],
                ];
                break;
            case 'send':
                $rules = [
                    'type'     => ['required', 'string', Rule::in(['friend', 'group'])], // 群消息还是好友友消息
                    'model_id' => ['required', 'integer'],//
                    'content'  => [
                        'required',
                        'array',
                        function ($attribute, $value, $fail) {
                            //todo 信息有效性进行验证
                            //todo type: text emoji file system img  see https://github.com/mattmezza/vue-beautiful-chat
                        }
                    ]
                ];
                break;
            case 'chatRecords':
                $rules = [
                    'type'     => ['required', 'string', Rule::in(['friend', 'group'])],
                    'model_id' => ['required', 'integer'],
                ];
                break;
            default:
                break;

        }
        return $rules;
    }
}
