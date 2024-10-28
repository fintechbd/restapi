<?php

namespace Fintech\RestApi\Traits;

trait UserRequestFieldTrait
{
    private array $userFields = [
        'name', 'mobile', 'email', 'login_id', 'password', 'pin',
        'language', 'currency', 'app_version', 'fcm_token', 'photo',
        'roles', 'parent_id',
    ];

    private array $profileFields = [
        'name', 'mobile', 'email', 'login_id', 'password', 'pin',
        'language', 'currency', 'app_version', 'fcm_token', 'photo',
        'roles', 'parent_id',
    ];
}
