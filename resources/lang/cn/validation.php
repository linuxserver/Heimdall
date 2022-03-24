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

    'accepted'             => '必须同意 :attribute.',
    'active_url'           => ':attribute 不是一个有效的网址.',
    'after'                => ':attribute 必须在 :date 之后.',
    'after_or_equal'       => ':attribute 必须是 :date 或在其之后.',
    'alpha'                => ':attribute 只能包含英文字母.',
    'alpha_dash'           => ':attribute 只能包含英文字母, 数字或"-"符号.',
    'alpha_num'            => ':attribute 只能包含英文字母与数字.',
    'array'                => ':attribute 只能是一个数组.',
    'before'               => ':attribute 必须在 :date 之前.',
    'before_or_equal'      => ':attribute 必须是 :date 或在其之前.',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 到 :max 之间.',
        'file'    => ':attribute 必须在 :min 到 :max 千字节 之间.',
        'string'  => ':attribute 必须在 :min 到 :max 个字符 之间.',
        'array'   => ':attribute 必须在 :min 到 :max 个项 之间.',
    ],
    'boolean'              => ':attribute 只能是真或假.',
    'confirmed'            => ':attribute 确认不匹配.',
    'date'                 => ':attribute 是无效日期.',
    'date_format'          => ':attribute 不符合 :format 的格式.',
    'different'            => ':attribute 与 :other 必须不一致.',
    'digits'               => ':attribute 必须是 :digits 位.',
    'digits_between'       => ':attribute 必须在 :min 到 :max 位 之间.',
    'dimensions'           => ':attribute 图像尺寸无效.',
    'distinct'             => ':attribute 字段有重复值.',
    'email'                => ':attribute 必须是有效的邮箱地址.',
    'exists'               => '选中的 :attribute 已存在.',
    'file'                 => ':attribute 只能是文件.',
    'filled'               => ':attribute 必须填写.',
    'image'                => ':attribute 只能是图片.',
    'in'                   => '选中的 :attribute 无效.',
    'in_array'             => ':attribute 必须不存在于 :other 中.',
    'integer'              => ':attribute 只能是整数.',
    'ip'                   => ':attribute 只能是 IP 地址.',
    'ipv4'                 => ':attribute 必须是有效的 IPv4 地址.',
    'ipv6'                 => ':attribute 必须是有效的 IPv6 地址.',
    'json'                 => ':attribute 必须是有效的 JSON 字符串.',
    'max'                  => [
        'numeric' => ':attribute 不应比 :max 大.',
        'file'    => ':attribute 不应比 :max 千字节 多.',
        'string'  => ':attribute 不应比 :max 个字符 多.',
        'array'   => ':attribute 不应有多于 :max 个项.',
    ],
    'mimes'                => ':attribute 必须是 :values 类型的文件.',
    'mimetypes'            => ':attribute 必须是 :values 类型的文件.',
    'min'                  => [
        'numeric' => ':attribute 不应比 :min 小.',
        'file'    => ':attribute 不应比 :min 千字节 少.',
        'string'  => ':attribute 不应比 :min 个字符 少.',
        'array'   => ':attribute 不应有多于 :min 个项.',
    ],
    'not_in'               => '选中的 :attribute 无效.',
    'numeric'              => ':attribute 只能是数字.',
    'present'              => ':attribute 字段必须存在.',
    'regex'                => ':attribute 格式无效.',
    'required'             => ':attribute 字段必须填写.',
    'required_if'          => ':attribute 字段必须填写, 因 :other 是 :value.',
    'required_unless'      => ':attribute 字段必须填写, 除非 :other 在 :values 中.',
    'required_with'        => ':attribute 字段必须填写, 因 :values 存在.',
    'required_with_all'    => ':attribute 字段必须填写, 因 :values 存在.',
    'required_without'     => ':attribute 字段必须填写, 因 :values 不存在.',
    'required_without_all' => ':attribute 字段必须填写, 因任何 :values 均不存在.',
    'same'                 => ':attribute 与 :other 必须相符.',
    'size'                 => [
        'numeric' => ':attribute 必须是 :size.',
        'file'    => ':attribute 必须是 :size 千字节.',
        'string'  => ':attribute 必须是 :size 个字符.',
        'array'   => ':attribute 必须包含 :size 个项.',
    ],
    'string'               => ':attribute 必须是字符串.',
    'timezone'             => ':attribute 必须是有效时区.',
    'unique'               => ':attribute 已被占用.',
    'uploaded'             => ':attribute 上传失败.',
    'url'                  => ':attribute 格式无效.',

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
            'rule-name' => '自定义信息',
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
