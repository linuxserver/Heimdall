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

    'accepted'             => ':attribute moet geaccepteerd worden.',
    'active_url'           => ':attribute is geen geldige URL.',
    'after'                => ':attribute moet een datum na :date zijn.',
    'after_or_equal'       => ':attribute moet een datum op of na :date zijn.',
    'alpha'                => ':attribute mag alleen letters bevatten.',
    'alpha_dash'           => ':attribute mag alleen letters, cijfers en streepjes bevatten.',
    'alpha_num'            => ':attribute mag alleen letters en cijfers bevatten.',
    'array'                => ':attribute moet een array zijn.',
    'before'               => ':attribute moet een datum voor :date zijn.',
    'before_or_equal'      => ':attribute moet een datum op of voor :date zijn.',
    'between'              => [
        'numeric' => ':attribute moet tussen :min en :max liggen.',
        'file'    => ':attribute moet tussen :min en :max kilobyte in omvang zijn.',
        'string'  => ':attribute moet tussen :min en :max karakters bevatten.',
        'array'   => ':attribute moet tussen :min en :max items bevatten.',
    ],
    'boolean'              => 'Het veld :attribute moet waar of onwaar zijn.',
    'confirmed'            => 'De bevestiging voor :attribute komt niet overeen.',
    'date'                 => ':attribute is geen geldige datum.',
    'date_format'          => ':attribute komt niet overeen met het formaat :format.',
    'different'            => ':attribute en :other moeten verschillen.',
    'digits'               => ':attribute moet :digits getallen bevatten.',
    'digits_between'       => ':attribute moet tussen :min en :max getallen bevatten.',
    'dimensions'           => ':attribute heeft ongeldige afbeeldingsafmetingen.',
    'distinct'             => 'Het veld :attribute heeft een dubbele waarde.',
    'email'                => ':attribute moet een geldig e-mailadres zijn.',
    'exists'               => 'Geselecteerde :attribute is ongeldig.',
    'file'                 => ':attribute moet een bestand zijn.',
    'filled'               => 'Het veld :attribute moet een waarde bevatten.',
    'image'                => ':attribute moet een afbeelding zijn.',
    'in'                   => 'Geselecteerde :attribute is ongeldig.',
    'in_array'             => 'Het veld :attribute bestaat niet in :other.',
    'integer'              => ':attribute moet een geheel getal zijn.',
    'ip'                   => ':attribute moet een geldig IP-adres zijn.',
    'ipv4'                 => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6'                 => ':attribute moet een geldig IPv6-adres zijn.',
    'json'                 => ':attribute moet een geldige JSON-tekenreekswaarde zijn.',
    'max'                  => [
        'numeric' => ':attribute mag niet groter dan :max zijn.',
        'file'    => ':attribute mag niet groter dan :max kilobyte in omvang zijn.',
        'string'  => ':attribute mag niet meer dan :max karakters bevatten.',
        'array'   => ':attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => ':attribute moet een bestand zijn van type: :values.',
    'mimetypes'            => ':attribute moet een bestand zijn van type: :values.',
    'min'                  => [
        'numeric' => ':attribute moet tenminste :min zijn.',
        'file'    => ':attribute moet tenminste :min kilobyte in omvang zijn.',
        'string'  => ':attribute moet tenminste :min karakters bevatten.',
        'array'   => ':attribute moet tenminste :min items bevatten.',
    ],
    'not_in'               => 'Geselecteerde :attribute is ongeldig.',
    'numeric'              => ':attribute moet een getal zijn.',
    'present'              => 'Het veld :attribute moet aanwezig zijn.',
    'regex'                => 'Het formaat van :attribute is ongeldig.',
    'required'             => 'Het veld :attribute is vereist.',
    'required_if'          => 'Het veld :attribute is vereist wanneer :other :value is.',
    'required_unless'      => 'Het veld :attribute is vereist tenzij :other in :values aanwezig is.',
    'required_with'        => 'Het veld :attribute is vereist wanneer :values aanwezig is.',
    'required_with_all'    => 'Het veld :attribute is vereist wanneer :values aanwezig is.',
    'required_without'     => 'Het veld :attribute is vereist wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het veld :attribute is vereist wanneer geen van :values aanwezig zijn.',
    'same'                 => ':attribute en :other moeten overeenkomen.',
    'size'                 => [
        'numeric' => ':attribute moet :size zijn.',
        'file'    => ':attribute moet :size kilobyte in omvang zijn.',
        'string'  => ':attribute moet :size karakters bevatten.',
        'array'   => ':attribute moet :size items bevatten.',
    ],
    'string'               => ':attribute moet een tekstformaat zijn.',
    'timezone'             => ':attribute moet een geldige tijdzone bevatten.',
    'unique'               => ':attribute is reeds in gebruik.',
    'uploaded'             => 'Het uploaden van :attribute is niet gelukt.',
    'url'                  => 'Het formaat van :attribute is ongeldig.',

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
