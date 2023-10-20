<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class Jobs implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('jobs', function (Mapping $mapping, Settings $settings) {
            $settings->analysis([
                "filter" => [
                    "license_synomyms" => [
                        "type" => "synonym",
                        "synonyms" => [
                            "B, B1 => autoload",
                            "C => tõstukiload, rekkaload, veoautoload",
                            "CE => tõstukiload, rekkaload, veoautoload",
                            "D, D1 => bussijuhi load"
                        ]
                    ],
                    "job_requirements_synomyms" => [
                        "type" => "synonym",
                        "synonyms" => [
                            "cnc operaator, cnc seadistaja, cnc pingi operaator => cnc",
                            "lihttööline => tootmistööline",
                            "montöör => monteerija",
                            "metall => metallitööline, plekksepp",
                            "puidu => puit",
                            "komplekteerija, ladu, laopidaja, logistik, laohoidja, laadija, veokorraldaja, operaator => laotööline",
                            "klienditeenindaja, saalitöötaja, kelner, sommeljee, baaridaam, baarman, baar, kassapidaja, kassiir, müüja, letiteenindaja, klienditugi => teenindaja",
                            "teenindusjuht, kaupluse juhataja, poejuhataja => vahetusevanem",
                            "ehitustooline, ehitaja, üldehitus, betoneerija, armeerija => ehitus",
                            "torulukksepp, sanitaartehnik => santehnik",
                            "elektritööline, elektritehnik => elektrik",
                            "mööblitisler, polsterdaja => tisler",
                            "kinnisvaramaakler, maakler, kinnisvarahindaja => kinnisvara",
                            "ostuassistent => ostuspetsialist",
                            "personalijuht, personalispetsialist, värbaja, värbamiskonsultant, värbamisspetsialist => hr",
                            "administraator, assistent, sekretär, juhiabi, büroojuht, kontorijuht, andmesisestaja => kontoritöötaja",
                            "müügijuht, müügiassistent, müügispetsialist, müügiesindaja, müügi => müük",
                            "tehasejuht tootmisjuht tiimijuht => tegevjuht",
                            "kvaliteedispetsialist, kvaliteedikontrolör => kvaliteedijuht",
                            "tööohutus, tööohutusspetsialist => tööohutuse spetsialist",
                            "finantsjuht, raamatupidaja, pearaamatupidaja, bookkeeper, palgaarvestaja, audiitor, finantskontroller => raamatupidamine",
                            "objektijuht => projektijuht",
                            "joonestaja => insener",
                            "turundusassistent, turundusjuht, turundaja, marketing specialist, turundusspetsialist, seo spetsialist, sisuturundaja => turundus",
                            "disainer, graafiline disainer, reklaamispetsialist, kujundaja, meedia planeerija => reklaam",
                            "front end, back end, full stack, moderaator, süsteemiadministraator, arendaja, developer => it",
                            "toateenija, puhastaja, puhastusteenindaja, puhastusspetsialist, nõudepesija => koristaja",
                            "peakokk, köögitööline, abikokk, koka abi, chef, köögi abitööline, kondiiter, pagar, küpsetaja, hommikusöögi teenindaja => kokk",
                            "taksojuht, autojuht, transporttööline => kuller",
                            "juuksur, küünetehnik, maniküür, pediküür, ripsmetehnik, kosmeetik, jumestaja, püsimeik, makeup, make-up, ilukonsultant, iluteenindaja => ilu",
                            "medõde, õde, arst, meditsiinitöötaja, kooliõde, parameedik, päästja, terapeut => meditsiin",
                            "vet, veterinaar, loomaarst, vet õde, veterinaarõde => veterinaaria",
                            "õpetaja, abiõpetaja, kasvataja, abikasvataja, logopeed => edu",
                            "coach, coaching, koolitusspetsialist, koolitaja => treener",
                            "spaa, spa, jõusaal, massöör, füsio, füsioterapeut => sport",
                            "rehvitehnik, automehaanik, töökojameister, autoremondilukksepp, töökojamees, ekspediitor, hooldustehnik, autopesija, autokeretehnik, mehaanik, lukksepp => auto",
                            "turvamees => turvatöötaja"
                        ]
                    ]
                ],
                'analyzer' => [
                    'default' => [
                        'type' => 'standard'
                    ],
                    // "default_search" => [
                    //     "type" =>  "job_requirements_analyzer"
                    // ],
                    'lincese_analyzer' => [
                        "tokenizer" => "standard",
                        "filter" => [
                            "lowercase",
                            "license_synomyms"
                        ]
                    ],
                    'job_requirements_analyzer' => [
                        "tokenizer" => "standard",
                        "filter" => [
                            "lowercase",
                            "job_requirements_synomyms",
                            "license_synomyms"
                        ]
                    ]
                ]
            ]);

            $mapping->long('id');
            $mapping->byte('status');
            $mapping->date('date');
            $mapping->text('desired_start_time', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->integer('required_candidates');
            $mapping->text('creator', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('contact_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('contact_email', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('contact_number', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->byte('training');
            $mapping->byte('observation');
            $mapping->text('desired_language_comment', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('salary', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('salary_type', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->integer('salary_amount_1');
            $mapping->integer('salary_amount_2');
            $mapping->integer('duration_type');
            $mapping->text('duration_type_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->integer('employment_type');
            $mapping->text('employment_type_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('job_type', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('job_type_comment', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('transport', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('transport_comment', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('working_hours', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('working_hours_comment', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('clothes', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('clothes_comment', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('shifts', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('company_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('offer_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('benefits', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('location', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('department', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('description', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('requirements', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('comments', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('additional_information', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('recess', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('working_languages', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('desired_languages', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->object('shifts_data', [
                'properties' => [
                    'start_time' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'end_time' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('jobs');
    }
}
