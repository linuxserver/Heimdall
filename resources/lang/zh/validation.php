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

    'accepted'             => '必须同意 :attribute',
    'active_url'           => ':attribute 不是一个有效的链接',
    'after'                => ':attribute 必须在 :date 之后',
    'after_or_equal'       => ':attribute 必须等于或在 :date 之后',
    'alpha'                => ':attribute 只能包含字母',
    'alpha_dash'           => ':attribute 只能包含字母,数字,下划线',
    'alpha_num'            => ':attribute 只能包含字母和数字',
    'array'                => ':attribute 必须是一个数组',
    'before'               => ':attribute 必须在 :date 之前',
    'before_or_equal'      => ':attribute 必须等于或在 :date 之前',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间',
        'file'    => ':attribute 必须在 :min 和 :max 千字节之间',
        'string'  => ':attribute 必须在 :min 和 :max 字母',
        'array'   => ':attribute 必须在 :min 和 :max 项',
    ],
    'boolean'              => ':attribute 字段只能是true/false',
    'confirmed'            => ':attribute 确认不一致',
    'date'                 => ':attribute 不是一个有效的日期',
    'date_format'          => ':attribute 格式不匹配 :format',
    'different'            => ':attribute 和 :other 必须不一致',
    'digits'               => ':attribute :digits 必须为数字',
    'digits_between'       => ':attribute 必须在 :min 和 :max 之间的数字',
    'dimensions'           => ':attribute 图片规格无效',
    'distinct'             => ':attribute 字段重复',
    'email'                => ':attribute 必须是一个有效的邮箱地址',
    'exists'               => '选中的 :attribute 是无效的',
    'file'                 => ':attribute 必须是一个文件',
    'filled'               => ':attribute 字段必须有值',
    'image'                => ':attribute 必须是图片',
    'in'                   => '选中的 :attribute 无效',
    'in_array'             => ':attribute 字段不存在 :other 中',
    'integer'              => ':attribute 必须是一个数字',
    'ip'                   => ':attribute 必须是一个IP地址',
    'ipv4'                 => ':attribute 必须是一个IPv4地址',
    'ipv6'                 => ':attribute 必须是一个IPv6地址',
    'json'                 => ':attribute 必须是一个有效的JSON字符串',
    'max'                  => [
        'numeric' => ':attribute 不可以大于 :max',
        'file'    => ':attribute 不可以大于 :max 千字节',
        'string'  => ':attribute 不可以大于 :max 字母',
        'array'   => ':attribute 不可以超出 :max 里的项',
    ],
    'mimes'                => ':attribute 文件类型必须为 :values',
    'mimetypes'            => ':attribute 文件类型必须为 :values',
    'min'                  => [
        'numeric' => ':attribute 必须至少为 :min',
        'file'    => ':attribute 必须至少为 :min 千字节',
        'string'  => ':attribute 必须至少为 :min 字符',
        'array'   => ':attribute 必须至少为 :min 项',
    ],
    'not_in'               => '选中的 :attribute 无效',
    'numeric'              => ':attribute 必须是一个数字',
    'present'              => ':attribute 字段必选',
    'regex'                => ':attribute 格式化无效',
    'required'             => ':attribute 字段必选',
    'required_if'          => ':attribute 当 :other 为 :value 时必选',
    'required_unless'      => ':attribute 必选除非 :other 在 :values 中',
    'required_with'        => ':attribute 当 :values 出现时必选',
    'required_with_all'    => ':attribute 当 :values 出现时必选',
    'required_without'     => ':attribute 当 :values 不存在时必选',
    'required_without_all' => ':attribute 当 :values 不存在时必选',
    'same'                 => ':attribute 和 :other 必须匹配',
    'size'                 => [
        'numeric' => ':attribute 必须为 :size',
        'file'    => ':attribute 必须为 :size 千字节',
        'string'  => ':attribute 必须为 :size 字母',
        'array'   => ':attribute 必须包含 :size 项',
    ],
    'string'               => ':attribute 必须是字符串',
    'timezone'             => ':attribute 必须是有效的时区',
    'unique'               => ':attribute 已经被使用了',
    'uploaded'             => ':attribute 上传失败',
    'url'                  => ':attribute 格式化无效',

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
            'rule-name' => '自定义消息',
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
