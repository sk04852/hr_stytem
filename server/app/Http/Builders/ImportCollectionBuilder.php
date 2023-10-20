<?php
namespace App\Http\Builders;

use App;
use App\Http\Builders\Builder;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class ImportCollectionBuilder implements Builder {

    private $moduleId_;
    public function __construct(int $moduleId) {
        $this->moduleId_ = $moduleId;
    }

    public function build() {
        switch($this->moduleId_) {
            case ModuleEnum::Products:
                return app()->make('App\Imports\ProductsImportCollection');
            break;
            case ModuleEnum::Clients:
                return app()->make('App\Imports\ClientsImportCollection');
            break;
        }
    }

    public function reset() {
        return null;
    }

}
?>

