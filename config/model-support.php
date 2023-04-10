<?php

return [
    /**
     * Sluggable model support configuration
     */
    'sluggable_fields' => [
        /**
         * The default slug field name
         * 
         * @var string
         * Ddefault: 'slug'
         */
        'slug' => 'slug',

        /**
         * The default title field name
         * 
         * @var string
         * Ddefault: 'title'
         */
        'title' => 'title',
    ],

    /**
     * HasActive model support configuration
     */
    'has_active' => [
        /**
         * The default is_active field name
         * 
         * @var string
         * Ddefault: 'is_active'
         */
        'is_active' => 'is_active',
    ],
];
