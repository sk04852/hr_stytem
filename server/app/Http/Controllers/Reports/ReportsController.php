<?php

namespace App\Http\Controllers\Reports;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Enums\ReportExportTypeEnum;
use App\Http\Services\FinancialTransactionsReport;
use Jimmyjs\ReportGenerator\Facades\PdfReportFacade as PdfReport;
use Jimmyjs\ReportGenerator\Facades\ExcelReportFacade as ExcelReport;
use Jimmyjs\ReportGenerator\Facades\CSVReportFacade as CSVReport;

class ReportsController extends Controller
{
    private $financialTransactionReport_;


    public function __construct(FinancialTransactionsReport $financialTransactionReport)
    {
        $this->financialTransactionReport_ = $financialTransactionReport;
    }

    public function tradingPlateFormAccount(Request $request)
    {
        try {
            return $this->financialTransactionReport_->tradingPlateFormReport($request);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function generateResponse($type, $queryBuilder, $title, $meta, $columns, $downloadFileName)
    {
        switch ($this->getReportExportType($type)) {
            case ReportExportTypeEnum::Json:
                return $queryBuilder->get()->toJson();
                break;
            case ReportExportTypeEnum::Pdf:
                return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->download($downloadFileName);
                break;
            case ReportExportTypeEnum::Excel:
                return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                    ->download($downloadFileName);
                break;
            case ReportExportTypeEnum::Csv:
                return CsvReport::of($title, $meta, $queryBuilder, $columns)
                    ->download($downloadFileName);
                break;
        }
    }

    private function getReportExportType(string $type = null)
    {
        $reportExportType = ReportExportTypeEnum::Json;
        switch ($type) {
            case "pdf":
                $reportExportType = ReportExportTypeEnum::Pdf;
                break;
            case "excel":
                $reportExportType = ReportExportTypeEnum::Excel;
                break;
            case "csv":
                $reportExportType = ReportExportTypeEnum::Csv;
                break;
        }
        return $reportExportType;
    }
}
