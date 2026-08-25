<?php

declare(strict_types=1);

return [
    'login_attempts' => (int) env('SECURITY_LOGIN_ATTEMPTS', 5),
    'login_decay' => (int) env('SECURITY_LOGIN_DECAY', 900),
];
