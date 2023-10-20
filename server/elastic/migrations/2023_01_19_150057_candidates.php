<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

use function PHPSTORM_META\type;

final class Candidates implements MigrationInterface
{
    const synonyms = [
        //Driving Licence
        'tõstukiload => C',
        'rekkaload => C',
        'veoautoload => C',
        'tõstukiload => CE',
        'rekkaload => CE',
        'veoautoload => CE',
        'autoload => B',
        'autoload => B1',
        'bussijuhi load => D',
        'bussijuhi load => D1',

    ];
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('candidates', function (Mapping $mapping, Settings $settings) {
            //Setting for Estonian
            // $settings->analysis([
            //     'filter' => [
            //         'estonian_stop' => [
            //             'type' => 'stop',
            //             'stopwords' => "_estonian_" //The default stopwords can be overridden with the stopwords or stopwords_path parameters.
            //         ],
            //         'estonian_keywords' => [
            //             'type' => 'keyword_marker',
            //             'keywords' => ["näide"] // This filter should be removed unless there are words which should be excluded from stemming
            //         ],
            //         'estonian_synonym' => [
            //             'type' => 'synonym',
            //             "lenient" => true,
            //             "synonyms" => self::synonyms
            //         ],
            //         'estonian_stemmer' => [
            //             'type' => 'stemmer',
            //             "language" => "estonian"
            //         ],
            //     ],
            //     'analyzer' => [
            //         //     'rebuilt_estonian' => [
            //         //         'tokenizer' => 'standard'
            //         //     ],
            //         //     'filter' => [
            //         //         'lowercase',
            //         //         'estonian_stop',
            //         //         'estonian_keywords',
            //         //         'estonian_synonym',
            //         //         'estonian_stemmer'
            //         //     ]
            //         'rebuilt_estonian' => [
            //             'type' => 'custom',
            //             'tokenizer' => 'standard',
            //             // 'char_filter' => [
            //             //     'html_strip',
            //             // ],
            //             'filter' => [
            //                 'lowercase',
            //                 'estonian_stop',
            //                 'estonian_keywords',
            //                 'estonian_synonym',
            //                 'estonian_stemmer'
            //             ],
            //         ],
            //     ]
            // ]);

            // $settings->analysis([
            //     'analyzer' => [
            //         'default' => [
            //             'type' => 'standard'
            //         ],
            //         'lincese_analyzer' => [
            //             "tokenizer" => "standard",
            //             "filter" => [
            //                 "lowercase",
            //                 "license_synomyms"
            //             ]
            //         ]
            //     ],
            //     "filter" => [
            //         "license_synomyms" => [
            //             "type" => "synonym",
            //             "synonyms" => [
            //                 "B, B1 => autoload",
            //                 "C => tõstukiload, rekkaload, veoautoload",
            //                 "CE => tõstukiload, rekkaload, veoautoload",
            //                 "D, D1 => bussijuhi load"
            //             ]
            //         ]
            //     ]
            // ]);

            $mapping->long('id');
            $mapping->text('first_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ]
                ]
            ]);
            $mapping->text('last_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->byte('gender');
            $mapping->text('gender_name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->date('dob');
            $mapping->text('phone', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('email', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('location', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('job_type', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('mother_language', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('description', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('source', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('personal_information', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ]
            ]);
            $mapping->text('personal_code', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->long('children_qty');
            $mapping->text('children_names', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('marital_status', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('desired_job', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->long('desired_salary');
            $mapping->text('desired_job_time', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('desired_job_location', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->long('age');
            $mapping->text('keywords', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('language', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('action', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('nationalities', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->object('driving_licenses', [
                'properties' => [
                    'level' => [
                        'type' => 'text',
                        // 'analyzer' => 'rebuilt_estonian',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'issue_data' => [
                        'type' => 'date',
                    ],
                    'expiry_data' => [
                        'type' => 'date',
                    ],
                ],
            ]);
            $mapping->text('recommendations', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('tags', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);

            $mapping->byte('status');

            $mapping->object('job_histories', [
                'properties' => [
                    'company_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'designation' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'ending_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'still_working' => [
                        'type' => 'byte',
                    ],
                    'work_place' => [
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
            $mapping->object('education', [
                'properties' => [
                    'degree' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'ending_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'institute' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'speciality' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'starting_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'still_studying' => [
                        'type' => 'byte',
                    ],
                    'additonal_information' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ]
                ],
            ]);
            $mapping->object('additional_courses', [
                'properties' => [
                    'description' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'ending_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'ending_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_day' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_month' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'starting_year' => [
                        'type' => 'short',
                        // 'fields' => [
                        //     'keyword' => [
                        //         'type' => 'keyword',
                        //         'ignore_above' => 256,
                        //     ],
                        // ],
                    ],
                    'title' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                    'total_hours' => [
                        'type' => 'byte'
                    ]
                ],
            ]);
            $mapping->text('skills', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ]);
            $mapping->text('languages', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256,
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
        Index::dropIfExists('candidates');
    }
}
