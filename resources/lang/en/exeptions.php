<?php
return [
    'guard' => 'The given role or permission should use guard `:expected` instead of `:given`.',
    'page' => [
        'url' => 'This page `:slug` is already exists for guard `:guardName`.',
        'ban' => 'This page `:slug` can\'t be child if the page with prefix like `post and etc.`. Choose another parent.'
    ],
    'postCategory' => [
        'url' => 'This post category `:slug` is already exists for guard `:guardName`.'
    ],
    'portfolioCategory' => [
        'url' => 'This portfolio category `:slug` is already exists for guard `:guardName`.'
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
