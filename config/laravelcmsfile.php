<?php

return [
    'strategies' => [
        /*
         * default strategy.
         */
        'default' => [
            /*
             * The form name for file.
             */
            'name' => 'file',

            /*
             * Allowed MIME types.
             */
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],


            /*
             * File size limit
             */
            'max_size' => '2m',

            /*
             * Strategy of filename.
             *
             * Available:
             *  - `random` Use random string as filename.
             *  - `md5_file` Use md5 of file as filename.
             *  - `original` Use the origin client file name.
             */
            'filename_type' => 'md5_file',
        ],


        //...
    ],
];
