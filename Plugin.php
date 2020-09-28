<?php namespace Jcc\Im;

use Backend;
use System\Classes\PluginBase;

/**
 * im Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'im',
            'description' => 'No description provided yet...',
            'author'      => 'jcc',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Jcc\Im\Contracts\Wbsocket\ImContract::class,\Jcc\Im\Services\ImService::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

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
        return []; // Remove this line to activate

        return [
            'jcc.im.some_permission' => [
                'tab' => 'im',
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
