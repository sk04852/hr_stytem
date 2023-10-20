<?php

namespace App\Http\Controllers\Search\Indexers;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class AccounstIndexConfigurator extends IndexConfigurator
{
    use Migratable;
    protected $name = 'accounts';
    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
