<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Disk
    |--------------------------------------------------------------------------
    |
    | Catalog and product photos are stored through Laravel's filesystem
    | abstraction. Local public disk is suitable for development. Production
    | may point this at object storage (for example the s3 disk) when the
    | filesystem is not persistently attached to a single server.
    |
    */

    'media_disk' => env('MEDIA_DISK', 'public'),

];
