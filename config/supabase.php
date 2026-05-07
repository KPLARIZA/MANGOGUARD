<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Supabase project credentials here.
    | Get these from your Supabase project settings: https://app.supabase.com
    |
    */

    'url' => env('SUPABASE_URL', ''),
    
    'anon_key' => env('SUPABASE_ANON_KEY', ''),
    
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', ''),

];
