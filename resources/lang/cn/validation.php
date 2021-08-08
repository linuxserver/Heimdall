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

    'accepted'             => ':attribute 必须被接受。',
    'active_url'           => ':attribute 不是一个有效的链接。',
    'after'                => ':attribute 必须晚于 :date。',
    'after_or_equal'       => ':attribute 必须晚于或等于 :date。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、数字和破折号。',
    'alpha_num'            => ':attribute 只能包含字母和数字。',
    'array'                => ':attribute 必须是一个数组。',
    'before'               => ':attribute 必须早于 :date。',
    'before_or_equal'      => ':attribute 必须早于或等于 :date。',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间。',
        'file'    => ':attribute 必须在 :min 和 :max 千字节之间。',
        'string'  => ':attribute 必须在 :min 和 :max 字母之间。',
        'array'   => ':attribute 必须在 :min 和 :max 项目之间。',
    ],
    'boolean'              => ':attribute 字段必须为 true 或 false。',
    'confirmed'            => ':attribute 确认不匹配。',
    'date'                 => ':attribute 不是一个有效的日期。',
    'date_format'          => ':attribute 不符合格式 :format。',
    'different'            => ':attribute 与 :other 必须不同。',
    'digits'               => ':attribute 必须是 :digits 位数。',
    'digits_between'       => ':attribute 的位数必须在 :min 和 :max 之间。',
    'dimensions'           => ':attribute 具有无效的图片尺寸。',
    'distinct'             => ':attribute 字段有重复的值。',
    'email'                => ':attribute 必须是一个有效的邮箱地址。',
    'exists'               => '选择的 :attribute 是无效的。',
    'file'                 => ':attribute 必须是一个文件。',
    'filled'               => ':attribute 字段必须有一个值。',
    'image'                => ':attribute 必须是图片。',
    'in'                   => '选择的 :attribute 无效。',
    'in_array'             => ':attribute 字段在 :other 中不存在。',
    'integer'              => ':attribute 必须是一个整数。',
    'ip'                   => ':attribute 必须是一个有效的 IP 地址。',
    'ipv4'                 => ':attribute 必须是一个有效的 IPv4 地址。',
    'ipv6'                 => ':attribute 必须是一个有效的 IPv6 地址。',
    'json'                 => ':attribute 必须是一个有效的 JSON 字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max。',
        'file'    => ':attribute 不能大于 :max 千字节。',
        'string'  => ':attribute 不能大于 :max 字母。',
        'array'   => ':attribute 不能大于 :max 项目。',
    ],
    'mimes'                => ':attribute 文件类型必须为： :values。',
    'mimetypes'            => ':attribute 文件类型必须为： :values。',
    'min'                  => [
        'numeric' => ':attribute 必须至少 :min。',
        'file'    => ':attribute 必须至少 :min 千字节。',
        'string'  => ':attribute 必须至少 :min 字母。',
        'array'   => ':attribute 必须至少含有 :min 项目。',
    ],
    'not_in'               => '选择的 :attribute 无效。',
    'numeric'              => ':attribute 必须是数字。',
    'present'              => ':attribute 字段必须存在。',
    'regex'                => ':attribute 格式无效。',
    'required'             => ':attribute 字段是必须的。',
    'required_if'          => ':attribute 字段是必须的当 :other 是 :value。',
    'required_unless'      => ':attribute 字段是必须的除非 :other 在 :values 之中。',
    'required_with'        => ':attribute 字段是必须的当 :values 存在。',
    'required_with_all'    => ':attribute 字段是必须的当 :values 存在。',
    'required_without'     => ':attribute 字段是必须的当 :values 不存在。',
    'required_without_all' => ':attribute 字段是必须的当 :values 一个都不存在。',
    'same'                 => ':attribute 和 :other 必须相符。',
    'size'                 => [
        'numeric' => ':attribute 必须为 :size。',
        'file'    => ':attribute 必须为 :size 千字节。',
        'string'  => ':attribute 必须为 :size 字母。',
        'array'   => ':attribute 必须为包含 :size 项目。',
    ],
    'string'               => ':attribute 必须是字符串。',
    'timezone'             => ':attribute 必须是有效的时区。',
    'unique'               => ':attribute 已被使用。',
    'uploaded'             => ':attribute 上传失败。',
    'url'                  => ':attribute 格式无效。',

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
