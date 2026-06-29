<?php

namespace Marvel\Jobs;

use Marvel\Database\Models\Import;
use Marvel\Imports\ProductsImport;
use Marvel\Services\Import\ProductImportService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 3600;

    public array $backoff = [60, 120, 240];

    protected int $importId;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
        $this->onQueue('high');
    }

    public function handle(): void
    {
        $import = Import::findOrFail($this->importId);
        $import->update(['status' => 'processing']);

        $filePath = Storage::disk('public')->path($import->file_path);

        $service = new ProductImportService();

        try {
            $importObj = new ProductsImport($service);

            $readerType = \Maatwebsite\Excel\Excel::XLSX;
            $extension = strtolower(pathinfo($import->file_name, PATHINFO_EXTENSION));
            if ($extension === 'xls') {
                $readerType = \Maatwebsite\Excel\Excel::XLS;
            } elseif ($extension === 'ods') {
                $readerType = \Maatwebsite\Excel\Excel::ODS;
            }

            Excel::import($importObj, $filePath, null, $readerType);

            $failedRows = $service->getFailedRows();
            $successCount = $service->getSuccessCount();

            $status = 'completed';
            if (!empty($failedRows) && $successCount > 0) {
                $status = 'completed_with_errors';
            } elseif (empty($failedRows) && $successCount === 0) {
                $status = 'failed';
            }

            $import->update([
                'status' => $status,
                'total_rows' => $successCount + count($failedRows),
                'processed_rows' => $successCount + count($failedRows),
                'success_rows' => $successCount,
                'failed_rows' => count($failedRows),
                'errors' => $failedRows,
            ]);

        } catch (Exception $e) {
            $import->update([
                'status' => 'failed',
                'errors' => [['sheet' => 'system', 'row' => 0, 'sku' => '', 'error_message' => $e->getMessage()]],
            ]);

            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        $import = Import::find($this->importId);
        if ($import) {
            $import->update(['status' => 'failed']);
        }
    }
}
