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

    /**
     * ParentChild model support configuration
     */
    'parent_child' => [
        /**
         * The default parent_id field name
         * 
         * @var string
         * Ddefault: 'parent_id'
         */
        'parent_id' => 'parent_id',
    ],

    /**
     * HasLanguage model support configuration
     */
    'has_language' => [
        /**
         * The default language field name
         * 
         * @var string
         * Ddefault: 'lang'
         */
        'language_field' => 'lang',
    ]
];
