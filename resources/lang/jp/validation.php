<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute を受け入れる必要があります。',
    'active_url'           => ':attribute は有効な URL ではありません。',
    'after'                => ':attribute は :date より後の日付でなければなりません。',
    'after_or_equal'       => ':attribute は :date 以降の日付でなければなりません。',
    'alpha'                => ':attribute には文字のみを含めることができます。',
    'accepted'              => ':attribute を受け入れる必要があります。',
    'active_url'            => ':attribute は有効な URL ではありません。',
    'after'                 => ':attribute は :date より後の日付でなければなりません。',
    'after_or_equal'        => ':attribute は :date 以降の日付でなければなりません。',
    'alpha'                 => ':attribute には文字のみを含めることができます。',
    'alpha_dash'            => ':attribute には、文字、数字、およびダッシュのみを含めることができます。',
    'alpha_num'             => ':attribute には、文字と数字のみを含めることができます。',
    'array'                 => ':attribute は配列でなければなりません。',
    'before'                => ':attribute は :date より前の日付でなければなりません。',
    'before_or_equal'       => ':attribute は :date より前または等しい日付でなければなりません。',
    'between'              => [
        'numeric' => ':attribute は :min と :max の間でなければなりません。',
        'file'     => ':attribute は :min から :max キロバイトの間でなければなりません。',
        'string'   => ':attribute は :min から :max 文字の間でなければなりません.',
        'array'    => ':attribute には :min と :max の項目が必要です。',
    ],
    'boolean'               => ':attribute フィールドは true または false でなければなりません。',
    'confirmed'             => ':attribute の確認が一致しません。',
    'date'                  => ':attribute は有効な日付ではありません。',
    'date_format'           => ':attribute がフォーマット :format と一致しません。',
    'different'             => ':attribute と :other は異なる必要があります。',
    'digits'                => ':attribute は :digits 桁でなければなりません。',
    'digits_between'        => ':attribute は :min から :max 桁の間でなければなりません.',
    'dimensions'            => ':attribute の画像サイズが無効です。',
    'distinct'              => ':attribute フィールドの値が重複しています。',
    'email'                 => ':attribute は有効な電子メール アドレスでなければなりません。',
    'exists'                => '選択された :attribute は無効です。',
    'file'                  => ':attribute はファイルでなければなりません。',
    'filled'                => ':attribute フィールドには値が必要です。',
    'image'                 => ':attribute は画像でなければなりません。',
    'in'                    => '選択された :attribute は無効です。',
    'in_array'              => ':attribute フィールドが :other に存在しません。',
    'integer'               => ':attribute は整数でなければなりません。',
    'ip'                    => ':attribute は有効な IP アドレスでなければなりません。',
    'ipv4'                  => ':attribute は有効な IPv4 アドレスでなければなりません。',
    'ipv6'                  => ':attribute は有効な IPv6 アドレスでなければなりません。',
    'json'                  => ':attribute は有効な JSON 文字列でなければなりません。',
    'max'                  => [
        'numeric' => ':attribute は :max を超えることはできません。',
        'file'     => ':attribute は :max キロバイトを超えることはできません。',
        'string'   => ':attribute は :max 文字を超えることはできません.',
        'array'    => ':attribute には :max 個を超えるアイテムを含めることはできません。',
    ],
    'mimes'                 => ':attribute は、タイプ: :values のファイルでなければなりません。',
    'mimetypes'             => ':attribute はタイプ: :values のファイルでなければなりません。',
    'min'                  => [
        'numeric' => ':attribute は少なくとも :min である必要があります。',
        'file'     => ':attribute は、少なくとも :min キロバイトでなければなりません。',
        'string'   => ':attribute は少なくとも :min 文字でなければなりません。',
        'array'    => ':attribute には少なくとも :min 個のアイテムが必要です。',
    ],
    'not_in'                => '選択された :attribute は無効です。',
    'numeric'               => ':attribute は数値でなければなりません。',
    'present'               => ':attribute フィールドが存在する必要があります。',
    'regex'                 => ':attribute 形式が無効です。',
    'required'              => ':attribute フィールドは必須です。',
    'required_if'           => ':other が :value の場合、:attribute フィールドは必須です。',
    'required_unless'       => ':values に :other がない限り、:attribute フィールドは必須です。',
    'required_with'         => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_with_all'     => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_without'      => ':values が存在しない場合、:attribute フィールドは必須です。',
    'regex'                 => ':attribute 形式が無効です。',
    'required_without_all' => ':values が存在しない場合、:attribute フィールドは必須です。',
    'regex'                 => ':attribute 形式が無効です。',
    'same'                  => ':attribute と :other は一致する必要があります。',
    'size'                 => [
        'numeric' => ':attribute は :size でなければなりません。',
        'file'     => ':attribute は :size キロバイトでなければなりません。',
        'string'   => ':attribute は :size 文字でなければなりません。',
        'array'    => ':attribute には :size 項目が含まれている必要があります。',
    ],
    'string'                => ':attribute は文字列でなければなりません。',
    'timezone'              => ':attribute は有効なゾーンでなければなりません。',
    'unique'                => ':attribute は既に取得されています。',
    'uploaded'              => ':attribute のアップロードに失敗しました。',
    'url'                   => ':attribute 形式が無効です。'

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
