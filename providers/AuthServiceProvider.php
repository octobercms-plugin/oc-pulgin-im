<?php

namespace Jcc\Im\Providers;

use Jcc\Im\Models\ChatRecord;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Jcc\Im\Policies\ChatRecordPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ChatRecord::class => ChatRecordPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
