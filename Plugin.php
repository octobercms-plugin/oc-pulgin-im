<?php namespace Jcc\Im;

use Backend;
use System\Classes\PluginBase;
use Jcc\Im\Models\Group;
use Jcc\Im\Models\GroupType;
use Jcc\Im\Models\MsgBox;
use Jcc\Im\Events\ImEventHandler;

/**
 * im Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Jcc\Im\Contracts\Wbsocket\ImContract::class, \Jcc\Im\Services\ImService::class);
        $this->app->bind(\Jcc\Im\Contracts\Wbsocket\ChatContract::class, \Jcc\Im\Services\ChatService::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \Event::subscribe(new ImEventHandler);
        \Jcc\Jwt\Models\User::extend(function ($model) {
            $model->hasMany['msgboxes']     = [
                MsgBox::class,
                'key' => 'user_id'
            ];
            $model->belongsToMany['groups'] = [
                Group::class,
                'table'    => 'jcc_im_user_groups',
                'key'      => 'user_id',
                'otherKey' => 'group_id'

            ];
            $model->belongsToMany['group_types'] = [
                GroupType::class,
                'table'    => 'jcc_im_user_group_types',
                'key'      => 'user_id',
                'otherKey' => 'group_type_id'

            ];
            $model->implement[]             = \Jcc\Im\Behaviors\UserImBehavior::class;
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Jcc\Im\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {

        return [
            'jcc.im.some_permission' => [
                'tab'   => 'im',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'im' => [
                'label'       => 'im',
                'url'         => Backend::url('jcc/im/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['jcc.im.*'],
                'order'       => 500,
            ],
        ];
    }
}
