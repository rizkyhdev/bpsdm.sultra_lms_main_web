<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LMS Feature Flags
    |--------------------------------------------------------------------------
    |
    | These flags control various features of the LMS system.
    |
    */

    /*
    | Hide enrollment CTA button outside enrollment window.
    | If true, the enrollment button will be hidden when enrollment is not available.
    | If false, the button will be shown but disabled with a tooltip.
    |
    */
    'hide_enroll_cta_outside_window' => env('HIDE_ENROLL_CTA_OUTSIDE_WINDOW', false),

    /*
    | Enable WebSocket countdown synchronization.
    | If true, the countdown will listen to Echo broadcasts for schedule updates.
    | If false, it will fall back to polling every 60 seconds.
    |
    */
    'enable_websocket_countdown_sync' => env('ENABLE_WEBSOCKET_COUNTDOWN_SYNC', true),
];

