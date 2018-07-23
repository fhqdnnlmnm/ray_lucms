<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PassportAccessTokenCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Laravel\Passport\Events\AccessTokenCreated $event
     * @return void
     */
    public function handle(\Laravel\Passport\Events\AccessTokenCreated $event)
    {
        $provider = \Config::get('auth.guards.api.provider');
        DB::table('oauth_access_token_providers')->insert([
            "oauth_access_token_id" => $event->tokenId,
            "provider" => $provider,
            "created_at" => new Carbon(),
            "updated_at" => new Carbon(),
        ]);
    }
}
