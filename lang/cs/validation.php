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

    'accepted'             => ':attribute musí být přijat.',
    'active_url'           => ':attribute není platná URL.',
    'after'                => ':attribute musí být datum po :date.',
    'after_or_equal'       => ':attribute musí být datum po nebo rovno :date.',
    'alpha'                => ':attribute může obsahovat pouze písmena.',
    'alpha_dash'           => ':attribute může obsahovat pouze písmena, čísla a pomlčku.',
    'alpha_num'            => ':attribute může obsahovat pouze písmena a čísla.',
    'array'                => ':attribute musí být pole.',
    'before'               => ':attribute musí být datum před :date.',
    'before_or_equal'      => ':attribute musí být datum před nebo rovno :date.',
    'between'              => [
        'numeric' => ':attribute musí být mezi :min a :max.',
        'file'    => ':attribute musí být mezi :min a :max kilobyty.',
        'string'  => ':attribute musí mít mezi :min a :max znaky.',
        'array'   => ':attribute musí mít mezi :min a :max položkami.',
    ],
    'boolean'              => ':attribute může být true nebo false.',
    'confirmed'            => ':attribute potvrzení se neshoduje.',
    'date'                 => ':attribute není platné datum.',
    'date_format'          => ':attribute neodpovídá formátu :format.',
    'different'            => ':attribute a :other se musí lišit.',
    'digits'               => ':attribute musí být :digits číslo.',
    'digits_between'       => ':attribute must be between :min and :max digits.',
    'dimensions'           => ':attribute má neplatné rozměry.',
    'distinct'             => ':attribute položka obsahuje duplicitní hodnoty.',
    'email'                => ':attribute musí být platná emailová adresa.',
    'exists'               => 'Vybraný :attribute je neplatný.',
    'file'                 => ':attribute musí být soubor.',
    'filled'               => ':attribute položka musí mít hodnotu.',
    'image'                => ':attribute musí být obrázek.',
    'in'                   => 'Vybraný :attribute je neplatný.',
    'in_array'             => ':attribute položka neexistuje v :other.',
    'integer'              => ':attribute musí být celočíselný.',
    'ip'                   => ':attribute musí být platná IP adresa.',
    'ipv4'                 => ':attribute musí být platná IPv4 adresa.',
    'ipv6'                 => ':attribute musí být platná IPv6 adresa.',
    'json'                 => ':attribute musí být validní JSON řetězec.',
    'max'                  => [
        'numeric' => ':attribute nemůže být větší než :max.',
        'file'    => ':attribute nemůže být větší než :max kilobytů.',
        'string'  => ':attribute nemůže být větší než :max znaků.',
        'array'   => ':attribute nemsí mít více než :max položek.',
    ],
    'mimes'                => ':attribute musí být soubor typu: :values.',
    'mimetypes'            => ':attribute musí být soubor typu: :values.',
    'min'                  => [
        'numeric' => ':attribute musí být nejméně :min.',
        'file'    => ':attribute musí mít nejméně :min kilobytů.',
        'string'  => ':attribute musí mít alespoň :min znaků.',
        'array'   => ':attribute musí mít nejméně :min položek.',
    ],
    'not_in'               => 'Vybraný :attribute je neplatný.',
    'numeric'              => ':attribute musí být číslo.',
    'present'              => ':attribute musí být přitomná.',
    'regex'                => ':attribute formát je neplatný.',
    'required'             => ':attribute je povinná.',
    'required_if'          => ':attribute je poviná pokud :other je :value.',
    'required_unless'      => ':attribute je poviná pokud :other je v :values.',
    'required_with'        => ':attribute je poviná pokud :values je přítomna.',
    'required_with_all'    => ':attribute je poviná pokud :values je přítomna.',
    'required_without'     => ':attribute je poviná pokud :values není přítomna.',
    'required_without_all' => ':attribute je poviná pokud žádná z :values není přítomná.',
    'same'                 => ':attribute a :other musí být stejné.',
    'size'                 => [
        'numeric' => ':attribute musí být :size.',
        'file'    => ':attribute musí být :size kilobytes.',
        'string'  => ':attribute musí být :size characters.',
        'array'   => ':attribute musí obsahovat :size items.',
    ],
    'string'               => ':attribute musí být řetězec.',
    'timezone'             => ':attribute musí být pltná časová zóna.',
    'unique'               => ':attribute musí být unikátní.',
    'uploaded'             => ':attribute se nepodařilo nahrát.',
    'url'                  => ':attribute formát je neplatný.',

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
