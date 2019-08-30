<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled?
    |--------------------------------------------------------------------------
    |
    | Enable or disable Omnisend integration.
    |
    | When disabled, events will not queue nor fire to Omnisend.
    |
    */

    'enabled' => env('OMNISEND_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your Omnisend API key.
    |
    */

    'api_key' => env('OMNISEND_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | The queue on which jobs will be processed.
    |
    */

    'queue' => env('OMNISEND_QUEUE', 'omnisend'),

    /*
    |--------------------------------------------------------------------------
    | Send Welcome Email
    |--------------------------------------------------------------------------
    |
    | If true, welcome emails will be fired to contacts upon contact
    | creation.
    |
    */

    'send_welcome_email' => env('OMNISEND_SEND_WELCOME_EMAIL', false),

    /*
    |--------------------------------------------------------------------------
    | Default Contact Status
    |--------------------------------------------------------------------------
    |
    | If true, welcome emails will be fired to contacts upon contact
    | creation.
    |
    */

    'default_contact_status' => env('OMNISEND_DEFAULT_CONTACT_STATUS', \Balfour\Omnisend\ContactStatus::SUBSCRIBED),

];
