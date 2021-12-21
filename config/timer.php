<?php

return [
    'delete_after_seconds' => 604800, // 7 days
    'status' => [ // Do not change this after going into production mode!
        'pending' => 1,
        'running' => 2,
        'done' => 8,
        'canceled' => 9,
    ]
];
