<?php

use App\Models\User;

test('user has a isactivated attribute', function () {
    $user = new User([
        'isactivated'  => 2
    ]);

    expect($user->isactivated)->toBe(2);
});
