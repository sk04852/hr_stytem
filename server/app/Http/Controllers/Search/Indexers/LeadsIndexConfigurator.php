<?php

namespace App\Http\Controllers\Search\Indexers;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class LeadsIndexConfigurator extends IndexConfigurator
{
    use Migratable;
    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
