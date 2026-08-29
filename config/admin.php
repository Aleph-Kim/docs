<?php

return [

    // 관리자 로그인 ID (평문)
    'id' => env('ADMIN_ID'),

    // 관리자 비밀번호 (평문)
    'password' => env('ADMIN_PASSWORD'),

    // API 업로드 전용 키 (미설정 시 password 사용)
    'api_key' => env('DOCS_API_KEY'),
];
