<?php

return [
    [
        'name' => 'Steadfasts',
        'flag' => 'steadfast.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'steadfast.create',
        'parent_flag' => 'steadfast.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'steadfast.edit',
        'parent_flag' => 'steadfast.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'steadfast.destroy',
        'parent_flag' => 'steadfast.index',
    ],
];
