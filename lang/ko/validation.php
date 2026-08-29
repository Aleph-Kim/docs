<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 유효성 검사 언어 라인 (Validation Language Lines)
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute을(를) 동의해야 합니다.',
    'accepted_if' => ':other이(가) :value일 때 :attribute을(를) 동의해야 합니다.',
    'active_url' => ':attribute은(는) 유효한 URL이어야 합니다.',
    'after' => ':attribute은(는) :date 이후 날짜여야 합니다.',
    'after_or_equal' => ':attribute은(는) :date 이후이거나 같은 날짜여야 합니다.',
    'alpha' => ':attribute은(는) 문자만 포함할 수 있습니다.',
    'alpha_dash' => ':attribute은(는) 문자, 숫자, 대시(-), 밑줄(_)만 포함할 수 있습니다.',
    'alpha_num' => ':attribute은(는) 문자와 숫자만 포함할 수 있습니다.',
    'any_of' => ':attribute의 값이 올바르지 않습니다.',
    'array' => ':attribute은(는) 배열이어야 합니다.',
    'array_keys' => ':attribute은(는) 다음 키만 포함해야 합니다: :values.',
    'ascii' => ':attribute은(는) 1바이트 영숫자 및 기호만 포함해야 합니다.',
    'base64' => ':attribute은(는) 올바른 Base64 문자열이어야 합니다.',
    'before' => ':attribute은(는) :date 이전 날짜여야 합니다.',
    'before_or_equal' => ':attribute은(는) :date 이전이거나 같은 날짜여야 합니다.',
    'between' => [
        'array' => ':attribute의 항목 수는 :min개에서 :max개 사이여야 합니다.',
        'file' => ':attribute의 용량은 :min킬로바이트에서 :max킬로바이트 사이여야 합니다.',
        'numeric' => ':attribute은(는) :min에서 :max 사이여야 합니다.',
        'string' => ':attribute은(는) :min자에서 :max자 사이여야 합니다.',
    ],
    'boolean' => ':attribute은(는) true 또는 false여야 합니다.',
    'can' => ':attribute에 허가되지 않은 값이 포함되어 있습니다.',
    'confirmed' => ':attribute 확인 항목이 일치하지 않습니다.',
    'contains' => ':attribute에 필수 항목이 누락되었습니다.',
    'current_password' => '비밀번호가 올바르지 않습니다.',
    'date' => ':attribute은(는) 유효한 날짜여야 합니다.',
    'date_equals' => ':attribute은(는) :date와 같은 날짜여야 합니다.',
    'date_format' => ':attribute은(는) :format 형식과 일치해야 합니다.',
    'decimal' => ':attribute은(는) 소수점 :decimal자리여야 합니다.',
    'declined' => ':attribute을(를) 거부해야 합니다.',
    'declined_if' => ':other이(가) :value일 때 :attribute을(를) 거부해야 합니다.',
    'different' => ':attribute과(와) :other은(는) 서로 달라야 합니다.',
    'digits' => ':attribute은(는) :digits자리 숫자여야 합니다.',
    'digits_between' => ':attribute은(는) :min자리에서 :max자리 사이의 숫자여야 합니다.',
    'dimensions' => ':attribute의 이미지 크기가 올바르지 않습니다.',
    'distinct' => ':attribute에 중복된 값이 있습니다.',
    'doesnt_contain' => ':attribute에 다음 값이 포함될 수 없습니다: :values.',
    'doesnt_end_with' => ':attribute은(는) 다음 값으로 끝날 수 없습니다: :values.',
    'doesnt_start_with' => ':attribute은(는) 다음 값으로 시작할 수 없습니다: :values.',
    'email' => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'encoding' => ':attribute은(는) :encoding 인코딩이어야 합니다.',
    'ends_with' => ':attribute은(는) 다음 중 하나로 끝나야 합니다: :values.',
    'enum' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'exists' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'extensions' => ':attribute은(는) 다음 확장자 중 하나여야 합니다: :values.',
    'file' => ':attribute은(는) 파일이어야 합니다.',
    'filled' => ':attribute에 값을 입력해야 합니다.',
    'gt' => [
        'array' => ':attribute의 항목 수는 :value개보다 많아야 합니다.',
        'file' => ':attribute의 용량은 :value킬로바이트보다 커야 합니다.',
        'numeric' => ':attribute은(는) :value보다 커야 합니다.',
        'string' => ':attribute은(는) :value자보다 길어야 합니다.',
    ],
    'gte' => [
        'array' => ':attribute의 항목 수는 :value개 이상이어야 합니다.',
        'file' => ':attribute의 용량은 :value킬로바이트 이상이어야 합니다.',
        'numeric' => ':attribute은(는) :value 이상이어야 합니다.',
        'string' => ':attribute은(는) :value자 이상이어야 합니다.',
    ],
    'hex_color' => ':attribute은(는) 유효한 16진수 색상이어야 합니다.',
    'image' => ':attribute은(는) 이미지여야 합니다.',
    'in' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'in_array' => ':attribute은(는) :other에 존재해야 합니다.',
    'in_array_keys' => ':attribute은(는) 다음 키 중 하나를 포함해야 합니다: :values.',
    'integer' => ':attribute은(는) 정수여야 합니다.',
    'ip' => ':attribute은(는) 유효한 IP 주소여야 합니다.',
    'ipv4' => ':attribute은(는) 유효한 IPv4 주소여야 합니다.',
    'ipv6' => ':attribute은(는) 유효한 IPv6 주소여야 합니다.',
    'json' => ':attribute은(는) 올바른 JSON 문자열이어야 합니다.',
    'list' => ':attribute은(는) 목록이어야 합니다.',
    'lowercase' => ':attribute은(는) 소문자여야 합니다.',
    'lt' => [
        'array' => ':attribute의 항목 수는 :value개 미만이어야 합니다.',
        'file' => ':attribute의 용량은 :value킬로바이트 미만이어야 합니다.',
        'numeric' => ':attribute은(는) :value보다 작아야 합니다.',
        'string' => ':attribute은(는) :value자보다 짧아야 합니다.',
    ],
    'lte' => [
        'array' => ':attribute의 항목 수는 :value개 이하여야 합니다.',
        'file' => ':attribute의 용량은 :value킬로바이트 이하여야 합니다.',
        'numeric' => ':attribute은(는) :value 이하여야 합니다.',
        'string' => ':attribute은(는) :value자 이하여야 합니다.',
    ],
    'mac_address' => ':attribute은(는) 유효한 MAC 주소여야 합니다.',
    'max' => [
        'array' => ':attribute의 항목 수는 최대 :max개여야 합니다.',
        'file' => ':attribute의 용량은 최대 :max킬로바이트여야 합니다.',
        'numeric' => ':attribute은(는) 최대 :max 이하여야 합니다.',
        'string' => ':attribute은(는) 최대 :max자 이하여야 합니다.',
    ],
    'max_digits' => ':attribute은(는) 최대 :max자리 이하여야 합니다.',
    'mimes' => ':attribute은(는) 다음 형식의 파일이어야 합니다: :values.',
    'mimetypes' => ':attribute은(는) 다음 형식의 파일이어야 합니다: :values.',
    'min' => [
        'array' => ':attribute의 항목 수는 최소 :min개여야 합니다.',
        'file' => ':attribute의 용량은 최소 :min킬로바이트여야 합니다.',
        'numeric' => ':attribute은(는) 최소 :min 이상이어야 합니다.',
        'string' => ':attribute은(는) 최소 :min자 이상이어야 합니다.',
    ],
    'min_digits' => ':attribute은(는) 최소 :min자리 이상이어야 합니다.',
    'missing' => ':attribute이(가) 없어야 합니다.',
    'missing_if' => ':other이(가) :value일 때 :attribute이(가) 없어야 합니다.',
    'missing_unless' => ':other이(가) :value이(가) 아니면 :attribute이(가) 없어야 합니다.',
    'missing_with' => ':values이(가) 있을 때 :attribute이(가) 없어야 합니다.',
    'missing_with_all' => ':values이(가) 모두 있을 때 :attribute이(가) 없어야 합니다.',
    'multiple_of' => ':attribute은(는) :value의 배수여야 합니다.',
    'not_in' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'not_regex' => ':attribute의 형식이 올바르지 않습니다.',
    'numeric' => ':attribute은(는) 숫자여야 합니다.',
    'password' => [
        'letters' => ':attribute에 문자가 최소 1개 이상 포함되어야 합니다.',
        'mixed' => ':attribute에 대문자와 소문자가 각각 최소 1개 이상 포함되어야 합니다.',
        'numbers' => ':attribute에 숫자가 최소 1개 이상 포함되어야 합니다.',
        'symbols' => ':attribute에 특수문자가 최소 1개 이상 포함되어야 합니다.',
        'uncompromised' => '입력한 :attribute이(가) 데이터 유출에 노출되었습니다. 다른 :attribute을(를) 사용해주세요.',
    ],
    'present' => ':attribute 필드가 있어야 합니다.',
    'present_if' => ':other이(가) :value일 때 :attribute 필드가 있어야 합니다.',
    'present_unless' => ':other이(가) :value이(가) 아니면 :attribute 필드가 있어야 합니다.',
    'present_with' => ':values이(가) 있을 때 :attribute 필드가 있어야 합니다.',
    'present_with_all' => ':values이(가) 모두 있을 때 :attribute 필드가 있어야 합니다.',
    'prohibited' => ':attribute 필드는 사용할 수 없습니다.',
    'prohibited_if' => ':other이(가) :value일 때 :attribute 필드는 사용할 수 없습니다.',
    'prohibited_if_accepted' => ':other을(를) 동의했을 때 :attribute 필드는 사용할 수 없습니다.',
    'prohibited_if_declined' => ':other을(를) 거부했을 때 :attribute 필드는 사용할 수 없습니다.',
    'prohibited_unless' => ':other이(가) :values에 없으면 :attribute 필드는 사용할 수 없습니다.',
    'prohibits' => ':attribute(으)로 인해 :other 필드를 사용할 수 없습니다.',
    'regex' => ':attribute 형식이 올바르지 않습니다.',
    'required' => ':attribute은(는) 필수입니다.',
    'required_array_keys' => ':attribute에는 다음 키에 대한 값이 포함되어야 합니다: :values.',
    'required_if' => ':other이(가) :value일 때 :attribute은(는) 필수입니다.',
    'required_if_accepted' => ':other을(를) 동의했을 때 :attribute은(는) 필수입니다.',
    'required_if_declined' => ':other을(를) 거부했을 때 :attribute은(는) 필수입니다.',
    'required_unless' => ':other이(가) :values에 없으면 :attribute은(는) 필수입니다.',
    'required_with' => ':values이(가) 있을 때 :attribute은(는) 필수입니다.',
    'required_with_all' => ':values이(가) 모두 있을 때 :attribute은(는) 필수입니다.',
    'required_without' => ':values이(가) 없을 때 :attribute은(는) 필수입니다.',
    'required_without_all' => ':values이(가) 모두 없을 때 :attribute은(는) 필수입니다.',
    'same' => ':attribute과(와) :other은(는) 일치해야 합니다.',
    'size' => [
        'array' => ':attribute의 항목 수는 :size개여야 합니다.',
        'file' => ':attribute의 용량은 :size킬로바이트여야 합니다.',
        'numeric' => ':attribute은(는) :size여야 합니다.',
        'string' => ':attribute은(는) :size자여야 합니다.',
    ],
    'starts_with' => ':attribute은(는) 다음 중 하나로 시작해야 합니다: :values.',
    'string' => ':attribute은(는) 문자열이어야 합니다.',
    'timezone' => ':attribute은(는) 올바른 표준 시간대여야 합니다.',
    'unique' => '이미 사용 중인 :attribute입니다.',
    'uploaded' => ':attribute 업로드에 실패했습니다.',
    'uppercase' => ':attribute은(는) 대문자여야 합니다.',
    'url' => ':attribute은(는) 올바른 URL 형식이어야 합니다.',
    'ulid' => ':attribute은(는) 올바른 ULID여야 합니다.',
    'uuid' => ':attribute은(는) 올바른 UUID여야 합니다.',

    /*
    |--------------------------------------------------------------------------
    | 커스텀 유효성 검사 언어 라인 (Custom Validation Language Lines)
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'title' => [
            'required' => '제목을 입력해주세요.',
            'max' => '제목은 최대 :max자까지 입력할 수 있습니다.',
        ],
        'slug' => [
            'alpha_dash' => '슬러그는 영문, 숫자, 대시(-), 밑줄(_)만 사용할 수 있습니다.',
            'max' => '슬러그는 최대 :max자까지 입력할 수 있습니다.',
            'unique' => '이미 사용 중인 슬러그입니다.',
        ],
        'category_id' => [
            'required' => '카테고리를 선택해주세요.',
            'exists' => '선택한 카테고리가 유효하지 않습니다.',
        ],
        'description' => [
            'max' => '한 줄 설명은 최대 :max자까지 입력할 수 있습니다.',
        ],
        'html_file' => [
            'required' => 'HTML 파일을 업로드해주세요.',
            'mimetypes' => 'HTML 파일은 html 또는 txt 형식만 지원합니다.',
            'max' => 'HTML 파일 용량은 최대 :max킬로바이트까지 업로드할 수 있습니다.',
        ],
        'name' => [
            'required' => '카테고리 이름을 입력해주세요.',
            'max' => '카테고리 이름은 최대 :max자까지 입력할 수 있습니다.',
        ],
        'id' => [
            'required' => '아이디를 입력해주세요.',
        ],
        'password' => [
            'required' => '비밀번호를 입력해주세요.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 커스텀 속성명 (Custom Validation Attributes)
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'title' => '제목',
        'slug' => '슬러그',
        'name' => '카테고리 이름',
        'category_id' => '카테고리',
        'category' => '카테고리',
        'description' => '한 줄 설명',
        'html_file' => 'HTML 파일',
        'id' => '아이디',
        'password' => '비밀번호',
        'api_key' => 'API 키',
        'file' => '파일',
        'url' => 'URL',
    ],

];

