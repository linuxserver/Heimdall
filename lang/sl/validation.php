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

    'accepted'             => ':attribute mora biti potrjen.',
    'active_url'           => ':attribute ni pravilen URL naslov.',
    'after'                => ':attribute mora biti datum po :date.',
    'after_or_equal'       => ':attribute mora biti datum po ali enak :date.',
    'alpha'                => ':attribute lahko vsebuje samo črke.',
    'alpha_dash'           => ':attribute lahko vsebuje samo črke, številke in pomišljaje.',
    'alpha_num'            => ':attribute lahko vsebuje samo črke in številke.',
    'array'                => ':attribute mora biti niz podatkov.',
    'before'               => ':attribute mora biti datum pred :date.',
    'before_or_equal'      => ':attribute mora biti datum pred ali enak :date.',
    'between'              => [
        'numeric' => ':attribute mora biti med :min in :max.',
        'file'    => ':attribute mora biti med :min in :max kilobajtov.',
        'string'  => ':attribute mora biti med :min in :max znakov.',
        'array'   => ':attribute mora biti med :min in :max elementov.',
    ],
    'boolean'              => ':attribute polje mora biti true ali false.',
    'confirmed'            => ':attribute potrditev se ne ujema.',
    'date'                 => ':attribute ni pravilen datum.',
    'date_format'          => ':attribute se ne ujema s formatom :format.',
    'different'            => ':attribute in :other morata biti različna.',
    'digits'               => ':attribute mora imeti :digits mest.',
    'digits_between'       => ':attribute mora biti med :in and :max mest.',
    'dimensions'           => ':attribute ima nepravilne dimenzije slike.',
    'distinct'             => ':attribute polje ima podvojeno vrednost.',
    'email'                => ':attribute mora biti pravilen e-naslov.',
    'exists'               => 'izbrani :attribute ni pravilen.',
    'file'                 => ':attribute mora biti datoteka.',
    'filled'               => ':attribute polje mora imeti vrednost.',
    'image'                => ':attribute mora biti slika.',
    'in'                   => 'izbrani :attribute ni pravilen.',
    'in_array'             => ':attribute polje ne obstaja v :other.',
    'integer'              => ':attribute mora biti številka.',
    'ip'                   => ':attribute mora biti veljaven IP naslov.',
    'ipv4'                 => ':attribute must be a valid IPv4 naslov.',
    'ipv6'                 => ':attribute must be a valid IPv6 naslov.',
    'json'                 => ':attribute mora biti veljaven JSON.',
    'max'                  => [
        'numeric' => ':attribute ne sme biti več kot :max.',
        'file'    => ':attribute ne sme biti več kot :max kilobajtov.',
        'string'  => ':attribute ne sme biti več kot :max znakov.',
        'array'   => ':attribute ne sme imeti več kot :max elementov.',
    ],
    'mimes'                => ':attribute mora biti datoteka tipa: :values.',
    'mimetypes'            => ':attribute mora biti datoteka tipa: :values.',
    'min'                  => [
        'numeric' => ':attribute mora biti najmanj :min.',
        'file'    => ':attribute mora biti najmanj :min kilobajtov.',
        'string'  => ':attribute mora imeti najmanj :min znakov.',
        'array'   => ':attribute mora imeti najmanj :min elementov.',
    ],
    'not_in'               => 'izbrani :attribute ni pravilen.',
    'numeric'              => ':attribute mora biti številka.',
    'present'              => ':attribute polje mora biti prisotno.',
    'regex'                => ':attribute format ni veljaven.',
    'required'             => ':attribute polje je obvezno.',
    'required_if'          => ':attribute polje je obvezno, če :other je :value.',
    'required_unless'      => ':attribute polje je obvezno, razen če :other je v :values.',
    'required_with'        => ':attribute polje je obvezno, če :values je prisotno.',
    'required_with_all'    => ':attribute polje je obvezno, če :values je prisotno.',
    'required_without'     => ':attribute polje je obvezno, ko :values ni prisotno.',
    'required_without_all' => ':attribute polje je obvezno, ko nobeden od :values ni prisotnih.',
    'same'                 => ':attribute in :other se morata ujemati.',
    'size'                 => [
        'numeric' => ':attribute mora biti :size.',
        'file'    => ':attribute mora biti :size kilobajtov.',
        'string'  => ':attribute mora imeti :size znakov.',
        'array'   => ':attribute mora imeti :size elementov.',
    ],
    'string'               => ':attribute mora biti niz.',
    'timezone'             => ':attribute mora biti veljavno časovna cona.',
    'unique'               => ':attribute je že zasedeno.',
    'uploaded'             => ':attribute nalaganje neuspešno.',
    'url'                  => ':attribute format ni pravilen.',

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
