<?php
return [
    'models' => [
        'user' => \App\User::class,
        'permission' => \SpaceCode\Maia\Models\Permission::class,
        'role' => \SpaceCode\Maia\Models\Role::class,
        'page' => \SpaceCode\Maia\Models\Page::class,
    ],
    'table_names' => [
        'users' => 'users',
        'pages' => 'pages',
        'roles' => 'roles',
        'permissions' => 'permissions',
        'settings' => 'settings',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    'permission' => [
        'column_names' => [
            'model_morph_key' => 'model_id',
        ],
        'display_permission_in_exception' => false,
        'cache' => [
            'expiration_time' => \DateInterval::createFromDateString('24 hours'),
            'key' => 'maia.permission.cache',
            'model_key' => 'name',
            'store' => 'default',
        ],
    ],
    'filemanager' => [
        'disk'      => env('FILEMANAGER_DISK', 'public'),
        'order'     => env('FILEMANAGER_ORDER', 'mime'),
        'direction' => env('FILEMANAGER_DIRECTION', 'asc'),
        'cache'     => env('FILEMANAGER_CACHE', false),
        'buttons'   => [
            'create_folder'   => true,
            'upload_button'   => true,
            'select_multiple' => true,
            'rename_folder'   => true,
            'delete_folder'   => true,
            'rename_file'     => true,
            'delete_file'     => true,
        ],
        'filters'   => [
            'Images'     => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'tiff'],
            'Documents'  => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx', 'odt', 'rtf', 'md', 'txt', 'css'],
            'Videos'     => ['mp4', 'avi', 'mov', 'mkv', 'wmv', 'flv', '3gp', 'h264'],
            'Audios'     => ['mp3', 'ogg', 'wav', 'wma', 'midi'],
            'Compressed' => ['zip', 'rar', 'tar', 'gz', '7z', 'pkg'],
        ],
        'filter'    => false,
        'naming'    => SpaceCode\Maia\Http\Services\DefaultNamingStrategy::class,
        'jobs'      => [],
    ],
];
