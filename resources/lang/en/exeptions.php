<?php
return [
    'guard' => 'The given role or permission should use guard `:expected` instead of `:given`.',
    'page' => [
        'reserved' => 'This page `:slug` reserved by System. Use another slug.',
        'url' => 'This page `:slug` is already exists for guard `:guardName`.',
        'ban' => 'This page `:slug` can\'t be child if the page with prefix like `post and etc.`. Choose another parent.'
    ],
    'product' => [
        'price' => 'Regular Price can\'t be 0.00',
        'discountPrice' => 'Discount Price can\'t be equal to the or more than Regular Price',
        'dateFromWithNow' => 'Start At Date can\'t be equal to the or less than Current Date',
        'dateToWithNow' => 'End At Date can\'t be equal to the or less than Current Date',
        'dateFromAddMinutes' => 'Start At Date must be greater than the Current Date at least on 5 minutes',
        'dateFromWithTo' => 'End At Date must be greater than Start At Date at least on 1 hour',
        'wholesale_price' => 'Wholesale Price can\'t be 0.00',
        'discountWholesale_price' => 'Wholesale Discount Price can\'t be equal to the or more than Wholesale Price',
        'wholesale_dateFromWithNow' => 'Wholesale Start At Date can\'t be equal to the or less than Current Date',
        'wholesale_dateToWithNow' => 'Wholesale End At Date can\'t be equal to the or less than Current Date',
        'wholesale_dateFromAddMinutes' => 'Wholesale Start At Date must be greater than the Current Date at least on 5 minutes',
        'wholesale_dateFromWithTo' => 'Wholesale End At Date must be greater than Wholesale Start At Date at least on 1 hour',
    ],
    'postCategory' => [
        'url' => 'This post category `:slug` is already exists for guard `:guardName`.'
    ],
    'productCategory' => [
        'url' => 'This product category `:slug` is already exists for guard `:guardName`.'
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
