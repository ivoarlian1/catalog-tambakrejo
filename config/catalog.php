<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Disk
    |--------------------------------------------------------------------------
    |
    | Catalog and product photos use the local public disk by default.
    | Serverless production deployments can use the Supabase Storage API
    | through the "supabase" media disk.
    |
    */

    'media_disk' => env('MEDIA_DISK', 'public'),

    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
        'storage_bucket' => env('SUPABASE_STORAGE_BUCKET', 'catalog-images'),
    ],

];
