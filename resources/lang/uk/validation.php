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

    'accepted'             => ':attribute має бути підтверджено.',
    'active_url'           => ':attribute містить некоректний URL.',
    'after'                => ':attribute має бути дата більше ніж :date.',
    'after_or_equal'       => ':attribute має бути дата більше або рівна :date.',
    'alpha'                => ':attribute має містити лише літери.',
    'alpha_dash'           => ':attribute має містити лише літери, цифри та тире.',
    'alpha_num'            => ':attribute має містити лише літери і цифри.',
    'array'                => ':attribute має бути масивом.',
    'before'               => ':attribute має бути дата менше :date.',
    'before_or_equal'      => ':attribute має бути дата менне або рівна :date.',
    'between'              => [
        'numeric' => ':attribute має буте в інтервалі від :min до :max.',
        'file'    => ':attribute має буте в інтервалі від :min до :max кілобайт.',
        'string'  => ':attribute має буте в інтервалі від :min до :max символів.',
        'array'   => ':attribute має містити :min і :max елементів.',
    ],
    'boolean'              => ':attribute поле поле має бути True або False.',
    'confirmed'            => ':attribute підтвердження не відповідає дійсності.',
    'date'                 => ':attribute некоректна дата.',
    'date_format'          => ':attribute не співпадає з форматом :format.',
    'different'            => ':attribute і :other мають відрізнятися.',
    'digits'               => ':attribute має містити :digits розрядів.',
    'digits_between'       => ':attribute має містити від :min до :max розрядів.',
    'dimensions'           => ':attribute має некоректну роздільну здатність.',
    'distinct'             => ':attribute поле має значення що дублюється.',
    'email'                => ':attribute має бути дійсною адресою email.',
    'exists'               => 'Обраний :attribute недійсний.',
    'file'                 => ':attribute має бути файлом.',
    'filled'               => ':attribute поле має бути завповнено.',
    'image'                => ':attribute має бути зображенням.',
    'in'                   => 'Обране :attribute невірно.',
    'in_array'             => ':attribute поле не має існувати в :other.',
    'integer'              => ':attribute має бути цілим.',
    'ip'                   => ':attribute має містити правильну адресу IP.',
    'ipv4'                 => ':attribute має містити правильну адресу IPv4.',
    'ipv6'                 => ':attribute має містити правильну адресу IPv6.',
    'json'                 => ':attribute має містити правильну строку JSON.',
    'max'                  => [
        'numeric' => ':attribute не може бути більше :max.',
        'file'    => ':attribute не може бути більше :max кілобайт.',
        'string'  => ':attribute не може бути більше :max символів.',
        'array'   => ':attribute не може бути більше :max елементів.',
    ],
    'mimes'                => ':attribute має бути файлом вида: :values.',
    'mimetypes'            => ':attribute має бути файлом вида: :values.',
    'min'                  => [
        'numeric' => 'The :attribute має бути як мінімум :min.',
        'file'    => 'The :attribute має бути :min кілобайт.',
        'string'  => 'The :attribute має бути :min символів.',
        'array'   => 'The :attribute має містити мінімум :min елементів.',
    ],
    'not_in'               => 'Обраний :attribute недійсний.',
    'numeric'              => ':attribute має бути числом.',
    'present'              => ':attribute поле має існувати.',
    'regex'                => ':attribute формат недійсний.',
    'required'             => ':attribute поле обов\'язкове.',
    'required_if'          => ':attribute поле потрібно у випадку коли :other є :value.',
    'required_unless'      => ':attribute поле потрібно у випадку за виключенням коли :other є :values.',
    'required_with'        => ':attribute поле потрібно у випадку :values існує.',
    'required_with_all'    => ':attribute поле потрібно у випадку :values існують.',
    'required_without'     => ':attribute поле потрібно у випадку :values не існує.',
    'required_without_all' => ':attribute поле потрібно у випадку коли жодне з :values не існує.',
    'same'                 => ':attribute і :other повинні співпадати.',
    'size'                 => [
        'numeric' => ':attribute має бути :size.',
        'file'    => ':attribute має бути кілобайт.',
        'string'  => ':attribute має бути символів.',
        'array'   => ':attribute має містити :size елементів.',
    ],
    'string'               => ':attribute має бути строкою.',
    'timezone'             => ':attribute має бути правильною часовою зоною.',
    'unique'               => ':attribute вже існує.',
    'uploaded'             => ':attribute помилка завантаження.',
    'url'                  => ':attribute недійсний формат.',

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
            'rule-name' => 'Довільне повідомлення',
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
