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

    'accepted'             => ':attribute musi zostać zaakceptowany.',
    'active_url'           => ':attribute nie jest prawidłowym adresem URL.',
    'after'                => ':attribute musi być datą następną po :date.',
    'after_or_equal'       => ':attribute musi być datą następną lub równą dacie :date.',
    'alpha'                => ':attribute może zawierać tylko litery.',
    'alpha_dash'           => ':attribute mogą zawierać tylko litery, cyfry i myślniki.',
    'alpha_num'            => ':attribute może zawierać tylko litery i cyfry.',
    'array'                => ':attribute musi być tablicą.',
    'before'               => ':attribute musi być datą wcześniejszą od daty :date.',
    'before_or_equal'      => ':attribute musi być datą wcześniejszą lub równą dacie :date.',
    'between'              => [
        'numeric' => 'Numer :attribute musi byc większy niż :min oraz mniejszy niż :max.',
        'file'    => 'Rozmiar pliku :attribute musi byc większy niż :min oraz mniejszy niż :max kilobajtów.',
        'string'  => 'Tekst :attribute musi posiadać więcej niż :min oraz mniej niż :max znaków.',
        'array'   => 'Tablica :attribute musi zawierać więcej niż :min oraz mniej niż :max elementów.',
    ],
    'boolean'              => ':attribute musi zwracac wartość logiczną TRUE lub FALSE.',
    'confirmed'            => ':attribute nie jest zgodny z polem potwierdzenia.',
    'date'                 => ':attribute nieprawidłowy format daty.',
    'date_format'          => 'Format daty :attribute musi byc zgodny z formatem :format.',
    'different'            => 'Wartości :attribute oraz :other muszą być różne.',
    'digits'               => 'Wartość :attribute musi być liczbą o długość :digits znaków.',
    'digits_between'       => 'Wartość :attribute musi być liczbą o długość co najmniej  :min oraz nie więcej niz :max digits.',
    'dimensions'           => ':attribute ma nieprawidłowe wymiary obrazu.',
    'distinct'             => 'Pole :attribute ma zduplikowaną wartość.',
    'email'                => ':attribute musi być prawidłowym adresem e-mail.',
    'exists'               => 'Wybrnay :attribute nie istnieje.',
    'file'                 => ':attribute musi być plikiem.',
    'filled'               => 'Pole :attribute nie może być puste.',
    'image'                => ':attribute musi być obrazem.',
    'in'                   => 'Wybrany :attribute jest nieprawidłowy.',
    'in_array'             => 'Pole :attribute nie istnieje w :other.',
    'integer'              => ':attribute musi być liczbą całkowitą.',
    'ip'                   => ':attribute musi być prawidłowym adresem IP.',
    'ipv4'                 => ':attribute musi być prawidłowym adresem IPv4.',
    'ipv6'                 => ':attribute musi być prawidłowym adresem IPv6.',
    'json'                 => ':attribute musi być poprawnym łańcuchem JSON.',
    'max'                  => [
        'numeric' => ':attribute nie może być większa niż :max.',
        'file'    => 'Rozmiar :attribute nie może być większy niż :max kilobajtów.',
        'string'  => ':attribute nie może zawierać więcej niż :max znaków.',
        'array'   => ':attribute nie może zawierać więcej niż :max elementów.',
    ],
    'mimes'                => ':attribute musi być plikiem typu: :values.',
    'mimetypes'            => ':attribute musi być plikiem typu: :values.',
    'min'                  => [
        'numeric' => ':attribute musi wynosić conajmniej :min.',
        'file'    => 'Rozmiar :attribute musi być rowny lub większy niż :min kilobajtów.',
        'string'  => ':attribute musi zawierać conajmniej :min znaków.',
        'array'   => ':attribute musi zawierać conajmniej :min elementów.',
    ],
    'not_in'               => ':attribute jest nieprawidłowy.',
    'numeric'              => ':attribute musi być liczbą.',
    'present'              => 'Obecność pola :attribute jest obowiązkowa.',
    'regex'                => 'Format :attribute jest nieprawidłowy.',
    'required'             => ':attribute jest wymagany.',
    'required_if'          => 'Pole :attribute jest wymagane gdy :other wynosi :value.',
    'required_unless'      => 'Pole :attribute jest wymagane, chyba że :other jest zawarte w :values.',
    'required_with'        => 'Pole :attribute jest wymagane gdy pole :values jest obecne.',
    'required_with_all'    => 'Pole :attribute jest wymagane gdy :values jest obecne.',
    'required_without'     => 'Pole :attribute jest wymagane gdy pole :values NIE jest obecne.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z pól :values NIE jest obecne.',
    'same'                 => 'Pole :attribute oraz :other muszą być takie same.',
    'size'                 => [
        'numeric' => ':attribute musi wynosić dokladnie :size.',
        'file'    => 'Rozmiar :attribute musi być równy :size kilobajtów.',
        'string'  => ':attribute musi składać się dokładnie z :size znaków.',
        'array'   => ':attribute musi składać się dokładnie z :size elementów.',
    ],
    'string'               => ':attribute musi być łańcuchem znaków.',
    'timezone'             => ':attribute musi być prawidłową strefą czasową.',
    'unique'               => ':attribute jest już zajety.',
    'uploaded'             => 'Nie udało się przesłać :attribute.',
    'url'                  => ':attribute ma nieprawidłowy format.',

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
            'rule-name' => 'dowlona-wiadomosc',
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
