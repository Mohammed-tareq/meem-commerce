<?php

namespace Marvel\Http\Controllers;

use App\Http\Controllers\Controller;
use Marvel\Database\Models\Import;
use Marvel\Http\Requests\ProductImportRequest;
use Marvel\Jobs\ImportProductsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Marvel\Enums\Permission;
use Marvel\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ProductImportController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:' . Permission::CREATE_PRODUCT . '|' . Permission::SUPER_ADMIN);
    }

    public function import(ProductImportRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $filePath = $file->store('imports', 'public');

        $import = Import::create([
            'type' => 'product',
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        ImportProductsJob::dispatch($import->id);

        return $this->apiResponse(__('message.MESSAGE.IMPORT_STARTED_SUCCESSFULLY'), 202, true, [
            'import_id' => $import->id,
            'status' => $import->status,
        ]);
    }

    public function status(int $id): JsonResponse
    {
        $import = Import::findOrFail($id);

        $total = $import->total_rows ?: 1;
        $progress = round(($import->processed_rows / $total) * 100, 2);

        return $this->apiResponse(__('message.MESSAGE.IMPORT_STATUS_FETCHED'), 200, true, [
            'id' => $import->id,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'success_rows' => $import->success_rows,
            'failed_rows' => $import->failed_rows,
            'progress' => min($progress, 100),
            'errors' => $import->errors,
        ]);
    }

    public function downloadErrors(int $id): BinaryFileResponse|JsonResponse
    {
        $import = Import::findOrFail($id);

        if (empty($import->errors)) {
            return $this->apiResponse(__('message.MESSAGE.IMPORT_NO_ERRORS'), 404, false);
        }

        $filename = "failed_import_rows_{$id}.xlsx";

        $errors = collect($import->errors);

        $export = new class($errors) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $errors;

            public function __construct($errors)
            {
                $this->errors = $errors;
            }

            public function collection()
            {
                return $this->errors->map(fn($e) => [
                    'sheet' => $e['sheet'] ?? '',
                    'row' => $e['row'] ?? '',
                    'sku' => $e['sku'] ?? '',
                    'error_message' => $e['error_message'] ?? '',
                ]);
            }

            public function headings(): array
            {
                return ['Sheet', 'Row', 'SKU', 'Error Message'];
            }
        };

        \Maatwebsite\Excel\Facades\Excel::store($export, $filename, 'local');

        return response()->download(
            storage_path("app/{$filename}"),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    public function cancel(int $id): JsonResponse
    {
        $import = Import::findOrFail($id);

        if (in_array($import->status, ['completed', 'completed_with_errors', 'failed', 'cancelled'], true)) {
            return $this->apiResponse(__('message.MESSAGE.IMPORT_CANNOT_CANCEL'), 409, false);
        }

        $import->update([
            'status' => 'cancelled',
        ]);

        return $this->apiResponse(__('message.MESSAGE.IMPORT_CANCELLED_SUCCESSFULLY'), 200, true, [
            'import_id' => $import->id,
            'status' => $import->status,
        ]);
    }

    public function downloadSample(): BinaryFileResponse
    {
        $samplePath = base_path('packages/marvel/resources/products/product-import-sample.xlsx');

        if (!file_exists($samplePath)) {
            throw new FileNotFoundException($samplePath);
        }

        return response()->download(
            $samplePath,
            'product-import-sample.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
