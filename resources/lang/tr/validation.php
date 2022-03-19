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

    'accepted'             => ':attribute kabul edilmelidir.',
    'active_url'           => ':attribute geçerli bir adres değil.',
    'after'                => ':attribute :date tarihinden sonra olmalıdır.',
    'after_or_equal'       => ':attribute :date ile aynı veya daha sonra tarihte olmalıdır.',
    'alpha'                => ':attribute sadece harf içerebilir.',
    'alpha_dash'           => ':attribute sadece harf, rakam, veya tire içerebilir.',
    'alpha_num'            => ':attribute sadece harf ve rakam içerebilir.',
    'array'                => ':attribute dizi olmalıdır.',
    'before'               => ':attribute :date tarihinden önce olmalıdır.',
    'before_or_equal'      => ':attribute :date ile aynı ya da daha önce tarihte olmalıdır.',
    'between'              => [
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'file'    => ':attribute :min ile :max kilobayt arasında olmalıdır.',
        'string'  => ':attribute :min ile :max arasında karakter içermelidir.',
        'array'   => ':attribute :min ile :max arasi öğe içermelidir.',
    ],
    'boolean'              => ':attribute alanı doğru ya da yanlış olmalıdır.',
    'confirmed'            => ':attribute onaylamasına uymuyor.',
    'date'                 => ':attribute geçerli bir tarih değil.',
    'date_format'          => ':attribute :format düzenine uymuyor.',
    'different'            => ':attribute ve :other farklı olmalı.',
    'digits'               => ':attribute :digits haneli olmalıdır.',
    'digits_between'       => ':attribute :min ile :max arası haneli olmalıdır.',
    'dimensions'           => ':attribute resim boyutları geçersiz.',
    'distinct'             => ':attribute alan değeri zaten mevcut.',
    'email'                => ':attribute geçerli bir eposta adresi olmalıdır.',
    'exists'               => 'Seçili :attribute geçersiz.',
    'file'                 => ':attribute dosya olmalıdır.',
    'filled'               => ':attribute alanı değer içermelidir.',
    'image'                => ':attribute resim olmalıdır.',
    'in'                   => 'Seçili :attribute geçersiz.',
    'in_array'             => ':attribute :other içinde bulunmalıdır.',
    'integer'              => ':attribute tamsayı olmalıdır.',
    'ip'                   => ':attribute geçerli IP adresi olmalıdır.',
    'ipv4'                 => ':attribute geçerli IPv4 adresi olmalıdır.',
    'ipv6'                 => ':attribute geçerli IPv6 adresi olmalıdır.',
    'json'                 => ':attribute geçerli JSON dizesi olmalıdır.',
    'max'                  => [
        'numeric' => ':attribute :max sayısından küçük olmalıdır.',
        'file'    => ':attribute :max kilobayttan küçük olmalıdır.',
        'string'  => ':attribute :max haneden az olmalıdır.',
        'array'   => ':attribute :max öğeden az içermelidir.',
    ],
    'mimes'                => 'Geçerli :attribute dosya tipi: :values.',
    'mimetypes'            => 'Geçerli :attribute dosya tipi: :values.',
    'min'                  => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file'    => ':attribute en az :min kilobayt olmalıdır.',
        'string'  => ':attribute en az :min haneli olmalıdır.',
        'array'   => ':attribute en az :min öğe içermelidir.',
    ],
    'not_in'               => 'Seçili :attribute geçersiz.',
    'numeric'              => ':attribute sayı olmalıdır.',
    'present'              => ':attribute alanı dolu olmalı.',
    'regex'                => ':attribute düzeni geçersiz.',
    'required'             => ':attribute alanı gereklidir.',
    'required_if'          => ':other :value ise :attribute alanı gereklidir.',
    'required_unless'      => ':other :values içinde değilse :attribute alanı gereklidir.',
    'required_with'        => ':values dolu ise :attribute alanı gereklidir.',
    'required_with_all'    => ':values dolu ise :attribute alanı gereklidir.',
    'required_without'     => ':values boş ise :attribute alanı gereklidir.',
    'required_without_all' => ':values değerlerinin tamamı boş ise :attribute alanı gereklidir.',
    'same'                 => ':attribute ve :other aynı olmalı.',
    'size'                 => [
        'numeric' => ':attribute :size olmalıdır.',
        'file'    => ':attribute :size kilobayt olmalıdır.',
        'string'  => ':attribute :size haneli olmalıdır.',
        'array'   => ':attribute :size öğe içermelidir.',
    ],
    'string'               => ':attribute dize olmalıdır.',
    'timezone'             => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique'               => ':attribute zaten kullanımda.',
    'uploaded'             => ':attribute yüklenemedi.',
    'url'                  => ':attribute düzeni geçersiz.',

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
