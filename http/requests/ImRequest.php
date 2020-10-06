<?php

namespace Jcc\Im\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Lang;

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
            case 'initData'://初始化好友群组信息
                $rules = [

                ];
                break;
            case 'bind'://前端连接wbsocket成功后
                $rules = [
                    'client_id' => ['required', 'string'],
                ];
                break;
            case 'send':
                $rules = [
                    'type'     => ['required', 'string', Rule::in(['friend', 'group'])], // 群消息还是好友友消息,还是系统消息
                    'content_type' => ['required', 'string',
                        Rule::in(['text', 'img', 'file', 'emoji'])], // 消息类型
                    'model_id' => ['required', 'integer'],//todo 在其他地方或这个文件中验证model_id的有效性
                    'content'  => [
                        'required',
                        'array',
                        function ($attribute, $value, $fail) {
                            //todo 信息有效性进行验证
                            //todo type: text emoji file system img

                            if (!is_array($value)) {
                                $fail('content参数错误');//todo 多语言
                            }
//                            if (!isset($value['message_type'])) {//
//                                $fail('content类型错误');//todo 多语言
//                            }
//                            if (in_array($value['message_type'], ['text', 'emoji', 'file', 'system'])) {
//                                $fail('content类型错误');//todo 多语言
//                            }
                            //去验证各个类型的信息
                            switch ($this->content_type) {
                                case 'text':
                                    if (!isset($value['value'])&&!$value['value']) {
                                        $fail('value类型错误');
                                    }
                                    break;
                                case 'img':
                                    if (!isset($value['value'])&&!$value['value']) {
                                        $fail('value类型错误');
                                    }
                                    break;
                                case 'file':
                                    if (!isset($value['value'])&&!$value['value']) {
                                        $fail('value类型错误');
                                    }
                                    break;
                                case 'emoji':
                                    if (!isset($value['value'])&&!$value['value']) {
                                        $fail('value类型错误');
                                    }
                                    break;
                            }
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
