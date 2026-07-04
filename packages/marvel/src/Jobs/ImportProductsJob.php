<?php

namespace Marvel\Jobs;

use Marvel\Database\Models\Import;
use Marvel\Exceptions\ImportCancelledException;
use Marvel\Imports\ProductsImport;
use Marvel\Services\Import\ProductImportService;
use Throwable;
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

        if ($import->status === 'cancelled') {
            return;
        }

        $import->update([
            'status' => 'processing',
            'total_rows' => $this->countRows(),
            'processed_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
        ]);

        $filePath = Storage::disk('public')->path($import->file_path);

        $service = new ProductImportService($this->importId);

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

            $service->finalizeProgress();

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
        } catch (ImportCancelledException $e) {
            $service->rollbackCreatedData();
            $import->update([
                'status' => 'cancelled',
                'processed_rows' => $service->getSuccessCount() + count($service->getFailedRows()),
                'success_rows' => $service->getSuccessCount(),
                'failed_rows' => count($service->getFailedRows()),
                'errors' => $service->getFailedRows(),
            ]);
        } catch (Throwable $e) {
            $import->update([
                'status' => 'failed',
                'errors' => [['sheet' => 'system', 'row' => 0, 'sku' => '', 'error_message' => $e->getMessage()]],
            ]);

            throw $e;
        }
    }

    protected function countRows(): int
    {
        try {
            $import = Import::find($this->importId);
            if (!$import) {
                return 0;
            }

            $filePath = Storage::disk('public')->path($import->file_path);

            $readerType = \Maatwebsite\Excel\Excel::XLSX;
            $extension = strtolower(pathinfo($import->file_name, PATHINFO_EXTENSION));
            if ($extension === 'xls') {
                $readerType = \Maatwebsite\Excel\Excel::XLS;
            } elseif ($extension === 'ods') {
                $readerType = \Maatwebsite\Excel\Excel::ODS;
            }

            $allRows = Excel::toArray(new ProductsImport(new ProductImportService()), $filePath, null, $readerType);

            $total = 0;
            foreach ($allRows as $sheetRows) {
                $total += count($sheetRows);
            }

            return $total;
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function failed(Throwable $exception): void
    {
        $import = Import::find($this->importId);
        if ($import && $import->status === 'processing') {
            $import->update(['status' => 'failed']);
        }
    }
}
