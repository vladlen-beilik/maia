<?php
return [
    'contactForm' => [
        'doesnotexist' => [
            'named' => 'There is no contact form with this title `:title`.',
            'withId' => 'There is no contact form with id `:id`.'
        ]
    ],
    'guard' => 'The given role or permission should use guard `:expected` instead of `:given`.',
    'page' => [
        'alreadyexist' => [
            'create' => 'This page `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no page with slug `:slug`.',
            'named' => 'There is no page with this title `:title`.',
            'withId' => 'There is no page with id `:id`.'
        ],
    ],
    'permission' => [
        'alreadyexist' => [
            'create' => 'A `:name` permission already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'create' => 'There is no permission named `:name` for guard `:guardName`.',
            'withId' => 'There is no permission with id `:id`.'
        ],
    ],
    'post' => [
        'alreadyexist' => [
            'create' => 'This post `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no post with slug `:slug`.',
            'named' => 'There is no post with this title `:title`.',
            'withId' => 'There is no post with id `:id`.'
        ]
    ],
    'postCategory' => [
        'alreadyexist' => [
            'create' => 'This post category `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no post category with slug `:slug`.',
            'named' => 'There is no post category with this title `:title`.',
            'withId' => 'There is no post category with id `:id`.'
        ],
    ],
    'postTag' => [
        'alreadyexist' => [
            'create' => 'This post tag `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no post tag with slug `:slug`.',
            'named' => 'There is no post tag with this title `:title`.',
            'withId' => 'There is no post tag with id `:id`.'
        ],
    ],
    'portfolio' => [
        'alreadyexist' => [
            'create' => 'This single portfolio `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no single portfolio with slug `:slug`.',
            'named' => 'There is no single portfolio with this title `:title`.',
            'withId' => 'There is no single portfolio with id `:id`.'
        ]
    ],
    'portfolioCategory' => [
        'alreadyexist' => [
            'create' => 'This portfolio category `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no portfolio category with slug `:slug`.',
            'named' => 'There is no portfolio category with this title `:title`.',
            'withId' => 'There is no portfolio category with id `:id`.'
        ],
    ],
    'portfolioTag' => [
        'alreadyexist' => [
            'create' => 'This portfolio tag `:slug` is already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'sluged' => 'There is no portfolio tag with slug `:slug`.',
            'named' => 'There is no portfolio tag with this title `:title`.',
            'withId' => 'There is no portfolio tag with id `:id`.'
        ],
    ],
    'role' => [
        'alreadyexist' => [
            'create' => 'A role `:name` already exists for guard `:guardName`.'
        ],
        'doesnotexist' => [
            'named' => 'There is no role named `:name`.',
            'withId' => 'There is no role with id `:id`.'
        ],
    ],
    'unauthorized' => [
        'forRoles' => [
            'message' => 'User does not have the right roles.',
            'message_value' => 'User does not have the right roles. Necessary roles are `:str`',
        ],
        'forPermissions' => [
            'message' => 'User does not have the right permissions.',
            'message_value' => 'User does not have the right permissions. Necessary permissions are `:str`',
        ],
        'forRolesOrPermissions' => [
            'message' => 'User does not have any of the necessary access rights.',
            'message_value' => 'User does not have the right permissions. Necessary permissions are `:str`',
        ],
        'notLoggedIn' => 'User is not logged in.'
    ],
    'slugmodelundefined' => 'Slug model undefined! Use slugModel() on the slug field!',
    'drivernotsupported' => 'Driver not supported. Please check your configuration'
];
