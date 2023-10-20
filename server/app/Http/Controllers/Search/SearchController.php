<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Services\GlobalSearchService;
use Illuminate\Http\Request;


class SearchController extends Controller
{
    const MODULE_NAME = 'search';
    const COLLECTION_NAME = 'searches';
    private $globalSearchService_;

    public function __construct(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService_ = $globalSearchService;
    }

    public function index(Request $request)
    {
        $keywords = (string) $request->keywords;
        return $this->globalSearchService_->search($keywords);
    }
}
