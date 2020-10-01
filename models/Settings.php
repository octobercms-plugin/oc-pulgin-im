<?php

namespace Jcc\Im\Models;

use Model;

class Settings extends Model
{
    /**
     * Model extensions
     *
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * Settings code
     *
     * @var string
     */
    public $settingsCode = 'jcc_im_settings';

    /**
     * Settings form
     *
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * Initial plugin settings
     *
     * @return void
     */
    public function initSettingsData()
    {

    }
}
