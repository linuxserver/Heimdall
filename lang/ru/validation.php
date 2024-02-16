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

    'accepted'             => ':attribute должен быть подтвержден.',
    'active_url'           => ':attribute содержит неверный URL.',
    'after'                => ':attribute должна быть дата больше :date.',
    'after_or_equal'       => ':attribute должна быть дата больше или равная :date.',
    'alpha'                => ':attribute может содержать только буквы.',
    'alpha_dash'           => ':attribute может содержать только буквы, цифры и тире.',
    'alpha_num'            => ':attribute может содержать только буквы и цифры.',
    'array'                => ':attribute должен быть массивом.',
    'before'               => ':attribute должна быть дата меньше :date.',
    'before_or_equal'      => ':attribute должна быть дата меньше или равная :date.',
    'between'              => [
        'numeric' => ':attribute должен быть в интервале :min и :max.',
        'file'    => ':attribute должен быть в интервале :min и :max килобайт.',
        'string'  => ':attribute должен быть в интервале :min и :max символов.',
        'array'   => ':attribute должен иметь :min и :max элементов.',
    ],
    'boolean'              => ':attribute поле должно быть Истина или Ложь.',
    'confirmed'            => ':attribute подтверждение не соответствует.',
    'date'                 => ':attribute неверная дата.',
    'date_format'          => ':attribute не совпадает с форматом :format.',
    'different'            => ':attribute и :other должны отличаться.',
    'digits'               => ':attribute должен иметь :digits разрядов.',
    'digits_between'       => ':attribute должен иметь :min и :max разрядов.',
    'dimensions'           => ':attribute имеет неверное разрешение.',
    'distinct'             => ':attribute поле имеет дублирующееся значение.',
    'email'                => ':attribute должен быть правильным адресом email.',
    'exists'               => 'Выбранный :attribute неверный.',
    'file'                 => ':attribute должен быть файлом.',
    'filled'               => ':attribute поле должно быть заполнено.',
    'image'                => ':attribute должно быть изображением.',
    'in'                   => 'Выбранное :attribute неверно.',
    'in_array'             => ':attribute поле не должно существовать в :other.',
    'integer'              => ':attribute должно быть целым.',
    'ip'                   => ':attribute должно содержать правильный адрес IP.',
    'ipv4'                 => ':attribute должно содержать правильный адрес IPv4.',
    'ipv6'                 => ':attribute должно содержать правильный адрес IPv6.',
    'json'                 => ':attribute должно содержать правильную строку JSON.',
    'max'                  => [
        'numeric' => ':attribute не может быть больше :max.',
        'file'    => ':attribute не может быть больше :max килобайт.',
        'string'  => ':attribute не может быть больше :max символов.',
        'array'   => ':attribute не может быть больше :max элементов.',
    ],
    'mimes'                => ':attribute должен быть файлом вида: :values.',
    'mimetypes'            => ':attribute должен быть файлом вида: :values.',
    'min'                  => [
        'numeric' => 'The :attribute должен быть как минимум :min.',
        'file'    => 'The :attribute должен быть :min килобайт.',
        'string'  => 'The :attribute должен быть :min символов.',
        'array'   => 'The :attribute должен иметь минимум :min элементов.',
    ],
    'not_in'               => 'Выбранный :attribute неверен.',
    'numeric'              => ':attribute должен быть числом.',
    'present'              => ':attribute поле должно существовать.',
    'regex'                => ':attribute формат неверен.',
    'required'             => ':attribute поле обязательно.',
    'required_if'          => ':attribute поле требуется в случае когда :other является :value.',
    'required_unless'      => ':attribute поле требуется в случае кроме :other является :values.',
    'required_with'        => ':attribute поле требуется в случае :values существует.',
    'required_with_all'    => ':attribute поле требуется в случае :values существует.',
    'required_without'     => ':attribute поле требуется в случае :values не существует.',
    'required_without_all' => ':attribute поле требуется в случае когда ни одно из :values не существует.',
    'same'                 => ':attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => ':attribute должен быть :size.',
        'file'    => ':attribute должен быть килобайт.',
        'string'  => ':attribute должен быть символов.',
        'array'   => ':attribute олжен содержать :size элементов.',
    ],
    'string'               => ':attribute должен быть строе=кой.',
    'timezone'             => ':attribute должна быть правильной зоной.',
    'unique'               => ':attribute уже существует.',
    'uploaded'             => ':attribute ошибка загрузки.',
    'url'                  => ':attribute неверный формат.',

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
            'rule-name' => 'Произвольное сообщение',
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
