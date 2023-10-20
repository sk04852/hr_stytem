<?php

/**
 * Setting String Folder wise
 */
return [
    'app' => [
        'Http' => [
            'Controllers' => [
                'CandidateCV' => [
                    'Services' => [
                        'CandidateAgreementService' => [
                            'download_container_exception' => 'Downloading Container Failed',
                            'agreement_file_missing' => 'Agreement file is missing',
                            'agrello_template_not_found' => 'Agrello Template data is Missing'
                        ]
                    ],
                    'CandidateCVAgreementsController' => [

                    ]
                ]
            ]
        ]
    ]
];
