<?php

namespace Jcc\Im\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;

class ChatRequest extends FormRequest
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
            case 'chatRecords':
                $rules = [
                    'type'     => ['required', Rule::in(['friend', 'group'])],
                    'model_id' => ['required'],
                ];
                break;
            default:
                break;

        }
        return $rules;
    }
}
