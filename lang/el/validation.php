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

    'accepted'             => 'Το πεδίο :attribute πρέπει να έχει γίνει αποδεκτό.',
    'active_url'           => 'Το πεδίο :attribute δέν είναι μία έγκυρη διεύθυνση URL.',
    'after'                => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία μετά από :date.',
    'after_or_equal'       => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία μετά από :date ή ίδια με :date.',
    'alpha'                => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματαμπορεί να περιέχει μόνο γράμματα.',
    'alpha_dash'           => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα, αριθμούς, και παύλες.',
    'alpha_num'            => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα και αριθμούς.',
    'array'                => 'Το πεδίο :attribute πρέπει να είναι ένας πίνακας.',
    'before'               => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία πρίν από :date.',
    'before_or_equal'      => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία πρίν από :date ή ίση με :date.',
    'between'              => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ από :min και :max.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι μεταξύ από :min και :max kilobytes.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι μεταξύ από :min και :max characters.',
        'array'   => 'Το πεδίο :attribute πρέπει να έχει μεταξύ :min και :max αντικείμενα.',
    ],
    'boolean'              => 'Το πεδίο :attribute πρέπει να είναι αληθές ή ψευδές.',
    'confirmed'            => 'Η επικύρωση του πεδίου :attribute δεν ταιριάζει.',
    'date'                 => 'Το πεδίο :attribute δεν είναι μία έγκυρη ημερομηνία.',
    'date_format'          => 'Το πεδίο :attribute δεν ταιριάζει με τη μορφή :format.',
    'different'            => 'Τα πεδία :attribute και :other πρέπει να είναι διαφορετικά.',
    'digits'               => 'Το πεδίο :attribute πρέπει να είναι :digits ψηφία.',
    'digits_between'       => 'Το πεδίο :attribute πρέπει να είναι μεταξύ από :min και :max ψηφία.',
    'dimensions'           => 'Το πεδίο :attribute δεν έχει έγκυρες διαστάσεις εικόνας.',
    'distinct'             => 'Το πεδίο :attribute έχει μία διπλότυπη τιμή.',
    'email'                => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση E-mail.',
    'exists'               => 'Το επιλεγμένο πεδίο :attribute είναι άκυρο.',
    'file'                 => 'Το πεδίο :attribute πρέπει να έιναι ένα αρχείο.',
    'filled'               => 'Το πεδίο :attribute πρέπει να έχει μία τιμή.',
    'image'                => 'Το πεδίο :attribute πρέπει να είναι μία εικόνα.',
    'in'                   => 'Το πεδίο selected :attribute είναι άκυρο.',
    'in_array'             => 'Το πεδίο :attribute δεν υπάρχει στο :other.',
    'integer'              => 'Το πεδίο :attribute πρέπει να είναι ένας ακέραιος αριθμός.',
    'ip'                   => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IP.',
    'ipv4'                 => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv4.',
    'ipv6'                 => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv6.',
    'json'                 => 'Το πεδίο :attribute πρέπει να είναι ένα έγκυρο JSON string.',
    'max'                  => [
        'numeric' => 'Το πεδίο :attribute δεν γίνεται να είναι μεγαλύτερο από :max.',
        'file'    => 'Το πεδίο :attribute δεν γίνεται να είναι μεγαλύτερο από :max kilobytes.',
        'string'  => 'Το πεδίο :attribute δεν γίνεται να είναι μεγαλύτερο από :max χαρακτήρες.',
        'array'   => 'Το πεδίο :attribute δεν γίνεται να έχει περισσότερα από :max αντικείμενα.',
    ],
    'mimes'                => 'Το πεδίο :attribute πρέπει να είναι ένα αρχείου τύπου: :values.',
    'mimetypes'            => 'Το πεδίο :attribute πρέπει να είναι ένα αρχείου τύπου: :values.',
    'min'                  => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min kilobytes.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min χαρακτήρες.',
        'array'   => 'Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min αντικείμενα.',
    ],
    'not_in'               => 'Το επιλεγμένο πεδίο :attribute είναι άκυρο.',
    'numeric'              => 'Το πεδίο :attribute πρέπει να ένας αριθμός.',
    'present'              => 'Το πεδίο :attribute πρέπει να είναι παρόν.',
    'regex'                => 'Η μορφή του πεδίου :attribute είναι άκυρη.',
    'required'             => 'Το πεδίο :attribute είναι απαιτούμενο.',
    'required_if'          => 'Το πεδίο :attribute είναι απαιτούμενο εάν :other είναι :value.',
    'required_unless'      => 'Το πεδίο :attribute είναι απαιτούμενο εκτός αν :other βρίσκεται στις τιμές :values.',
    'required_with'        => 'Το πεδίο :attribute είναι απαιτούμενο εάν οι τιμές :values είναι παρούσες.',
    'required_with_all'    => 'Το πεδίο :attribute είναι απαιτούμενο εάν οι τιμές :values είναι παρούσες.',
    'required_without'     => 'Το πεδίο :attribute είναι απαιτούμενο εάν οι τιμές :values δεν είναι παρούσες.',
    'required_without_all' => 'Το πεδίο :attribute είναι απαιτούμενο εάν καμία από τις τιμές :values δεν είναι παρούσες.',
    'same'                 => 'Το πεδίο :attribute και :other πρέπει να είναι ίδια.',
    'size'                 => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι :size.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι :size kilobytes.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι :size χαρακτήρες.',
        'array'   => 'Το πεδίο :attribute πρέπει να περιέχει :size αντικείμενα.',
    ],
    'string'               => 'Το πεδίο :attribute πρέπει να είναι ένα string.',
    'timezone'             => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη ζώνη ώρας.',
    'unique'               => 'Το πεδίο :attribute έχει ήδη χρησιμοποιηθεί.',
    'uploaded'             => 'Το πεδίο :attribute απέτυχε να μεταφορτωθεί.',
    'url'                  => 'Η μορφή του πεδίου :attribute είναι άκυρη.',

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
