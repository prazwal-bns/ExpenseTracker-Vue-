<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API requests per minute
    |--------------------------------------------------------------------------
    |
    | Authenticated requests are counted per user. Guests are counted per IP.
    |
    */

    'api' => 60,

    /*
    |--------------------------------------------------------------------------
    | Login and registration attempts per minute
    |--------------------------------------------------------------------------
    |
    | Counted per email address and IP together, so one account cannot be
    | guessed repeatedly and a shared network is not locked out as a whole.
    |
    */

    'auth' => 5,

    /*
    |--------------------------------------------------------------------------
    | Password change and account deletion attempts per minute
    |--------------------------------------------------------------------------
    |
    | Counted per authenticated user.
    |
    */

    'sensitive' => 5,

];
