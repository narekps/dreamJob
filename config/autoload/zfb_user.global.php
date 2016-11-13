<?php

return [
    'zfb_user' => [
        'module_options' => [
            'user_entity_class' => \App\Entity\User::class,
            'db_users_table' => 'users',
            'identity_column' => 'email',
            'credential_column' => 'password',
            'identity_confirm_column' => 'email_confirm',
            'enable_registration' => true,
            'storage_ttl' => 60 * 60 * 24, // 7 days
            'authentication_widget_view_tpl' => 'zfb-user/index/authentication_widget',
            'password_cost' => 14,
            'authentication_fail_message' => 'Authentication failed. Please try again.',
            'enable_identity_confirmation' => true,
            'crypt_salt' => 'SDFE%Y^&U*IUHGFE#@$%^&UYGHDFWE@#$%^&YUTGF',
        ],
        'authentication_form' => [
            'name_field_label' => 'Name',
            'name_field_name' => 'name',
            'identity_field_label' => 'E-mail',
            'identity_field_name' => 'identity',
            'credential_field_label' => 'Password',
            'credential_field_name' => 'credential',
            'submit_button_text' => 'Sign in',
            'csrf_timeout' => 60 * 3,
        ],
        'registration_form' => [
            'credential_verify_field_label' => 'Password Verify',
            'credential_verify_field_name' => 'credential_verify',
            'submit_button_text' => 'Sign up',
            'csrf_timeout' => 60 * 5,
        ],
        'authentication_controller' => [
            'authentication_callback_route' => 'profile',
            'logout_callback_route' => 'zfbuser',
            'authenticate_after_registration' => true,
        ],
    ]
];
