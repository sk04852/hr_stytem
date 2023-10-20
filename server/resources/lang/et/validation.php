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

    'accepted' => ':attribute peab olema vastu võetud.',
    'active_url' => ':attribute ei ole kehtiv URL.',
    'after' => ':attribute peab olema hiljem kui :date.',
    'after_or_equal' => ':attribute peab olema samaväärne või hiljem kui :date.',
    'alpha' => ':attribute võib sisaldada ainult tähti.',
    'alpha_dash' => ':attribute võib sisaldad ainult tähti, numbreid numbers, sidekriipse ja allkriipse.',
    'alpha_num' => ':attribute võib sisaldada ainult tähti ja numbreid.',
    'array' => ':attribute peab olema massiiv.',
    'before' => ':attribute peab olema kuupäev enne :date.',
    'before_or_equal' => ':attribute peab olema kuupäev enne või võrdne :date.',
    'between' => [
        'numeric' => ':attribute peab olema :min ja :max vahel.',
        'file' => ':attribute peab sisaldama :min kuni :max kilobaiti.',
        'string' => ':attribute peab sisaldama :min kuni :max tähemärki.',
        'array' => ':attribute peab sisaldama :min kuni :max elementi.',
    ],
    'boolean' => ':attribute field peab olema õige või vale.',
    'confirmed' => ':attribute kinnitus ei ühti.',
    'date' => ':attribute ei ole kehtiv kuupäev.',
    'date_equals' => ':attribute peab olema samaväärne :date.',
    'date_format' => ':attribute ei vasta vormingule :format.',
    'different' => ':attribute ja :other peavad olema erinevad.',
    'digits' => ':attribute peab olema :digits number.',
    'digits_between' => ':attribute numbrite arv peab olema :min vahel :max.',
    'dimensions' => ':attribute pildi mõõtmed ei sobi.',
    'distinct' => ':attribute väljal on topeltväärtus.',
    'email' => ':attribute peab olema kehtiv emaili aadress.',
    'ends_with' => ':attribute peab lõppema ühega järgmistest: :values.',
    'exists' => 'Valitud :attribute on vale.',
    'file' => ':attribute peab olema fail.',
    'filled' => ':attribute väljal peab olema väärtus.',
    'gt' => [
        'numeric' => ':attribute peab olema suurem kui :value.',
        'file' => ':attribute peal olema suurem kui :value kilobaiti.',
        'string' => ':attribute peab olema pikem kui :value tähemärki.',
        'array' => ':attribute peab olema rohkem kui :value elementi.',
    ],
    'gte' => [
        'numeric' => ':attribute peab olema võrdne või suurem kui :value.',
        'file' => ':attribute peal olema võrdne või suurem kui :value kilobaiti.',
        'string' => ':attribute peab olema võrdne või pikem kui :value tähemärki.',
        'array' => ':attribute peab olema võrdne arv või rohkem kui :value elementi.',
    ],
    'image' => ':attribute peab olema pilt.',
    'in' => 'Valitud atribuut :attribute on vale.',
    'in_array' => ':attribute välja ei eksisteeri :other.',
    'integer' => ':attribute peab olema täisarv.',
    'ip' => ':attribute peab olema kehtiv IP aadress.',
    'ipv4' => ':attribute peab olema kehtiv IPv4 aadress.',
    'ipv6' => ':attribute peab olema kehtiv IPv6 aadress.',
    'json' => ':attribute peab olema kehtiv JSON formaat.',
    'lt' => [
        'numeric' => ':attribute peab olema väiksem kui :value.',
        'file' => ':attribute peab olema väiksem kui :value kilobaiti.',
        'string' => ':attribute peab olema lühem kui :value tähemärki.',
        'array' => ':attribute peab sisaldama vähem kui :value elementi.',
    ],
    'lte' => [
        'numeric' => ':attribute peab olema võrdne või väiksem kui :value.',
        'file' => ':attribute peab olema võrdne või väiksem kui :value kilobaiti.',
        'string' => ':attribute peab olema võrdne või väiksem kui :value tähemärki.',
        'array' => ':attribute peab sisaldama vähem kui :value elementi.',
    ],
    'max' => [
        'numeric' => ':attribute ei tohi olla suurem kui :max.',
        'file' => ':attribute ei tohi olla suurem kui :max kilobaiti.',
        'string' => ':attribute ei tohi olla pikem kui :max tähemärki.',
        'array' => ':attribute ei tohi sisaldada rohkem kui :max elementi.',
    ],
    'mimes' => ':attribute peab olema fail: :values.',
    'mimetypes' => ':attribute peab olema fail: :values.',
    'min' => [
        'numeric' => ':attribute peab olema vähemalt :min.',
        'file' => ':attribute peab olema vähemalt :min kilobaiti.',
        'string' => ':attribute peab sisaldama vähemalt :min tähemärki.',
        'array' => ':attribute peab sisaldama vähemalt :min elementi.',
    ],
    'not_in' => 'Valitud :attribute on vale.',
    'not_regex' => ':attribute formaat on vale.',
    'numeric' => ':attribute peab olema number.',
    'password' => 'Parool on vale.',
    'present' => ':attribute väli peab eksisteerima.',
    'regex' => ':attribute formaat on vale.',
    'required' => ':attribute väli on kohustuslik.',
    'required_if' => ':attribute väli on kohustuslik, kui :other on :value.',
    'required_unless' => ':attribute väli on kohustuslik kuni :other on :values hulgas.',
    'required_with' => ':attribute väli on kohustuslik, kui :values eksisteerib.',
    'required_with_all' => ':attribute väli on kohustuslik, kui :values eksisteerivad.',
    'required_without' => ':attribute väli on kohustuslik, kui :values ei eksisteeri.',
    'required_without_all' => ':attribute väli on kohustuslik, kui ükski :values ei eksisteeri.',
    'same' => ':attribute ja :other peavad ühtima.',
    'size' => [
        'numeric' => ':attribute peab olema :size.',
        'file' => ':attribute peab olema :size kilobaiti.',
        'string' => ':attribute peab sisaldama :size tähemärki.',
        'array' => ':attribute peab sisaldama :size elementi.',
    ],
    'starts_with' => ':attribute peab algama ühega järgnevatest: :values.',
    'string' => ':attribute peab olema täht.',
    'timezone' => ':attribute peab olema kehtiv ajavöönd.',
    'unique' => ':attribute on juba kasutusel.',
    'uploaded' => ':attribute üleslaadimine ebaõnnestus.',
    'url' => ':attribute formaat on vale.',
    'uuid' => ':attribute peab olema kehtiv UUID.',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
