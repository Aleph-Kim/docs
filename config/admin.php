<?php

return [

    // 관리자 로그인 ID (평문)
    'id' => env('ADMIN_ID'),

    // 관리자 비밀번호의 bcrypt 해시. 생성: php artisan tinker --execute="echo bcrypt('비밀번호');"
    'password_hash' => env('ADMIN_PASSWORD_HASH'),

];
