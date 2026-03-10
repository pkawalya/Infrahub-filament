<?php

namespace App\Filament\App\Concerns;

use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Adds a "Bulk Import" header action to any Filament ListRecords page.
 *
 * Usage in a List page:
 *
 *   use App\Filament\App\Concerns\BulkImportsFromCsv;
 *
 *   class ListTasks extends ListRecords {
 *       use BulkImportsFromCsv;
 *
 *       protected function importModel(): string { return \App\Models\Task::class; }
 *       protected function importColumns(): array {
 *           return ['title', 'description', 'status', 'priority', 'assigned_to', 'start_date', 'end_date'];
 *       }
 *       protected function importRules(): array {
 *           return ['title' => 'required|string|max:255', 'status' => 'nullable|string'];
 *       }
 *   }
 */
trait BulkImportsFromCsv
{
    /**
     * Define the Eloquent model class for import.
     */
    abstract protected function importModel(): string;

    /**
     * Define allowed columns (CSV headers must match).
     */
    abstract protected function importColumns(): array;

    /**
     * Optional validation rules per row.
     */
    protected function importRules(): array
    {
        return [];
    }

    /**
     * Optional: additional data merged into each row (e.g., company_id).
     */
    protected function importDefaults(): array
    {
        return [
            'company_id' => auth()->user()?->company_id,
        ];
    }

    /**
     * Get the bulk import action.
     */
    protected function getBulkImportAction(): Actions\Action
    {
        return Actions\Action::make('bulkImport')
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->form([
                Forms\Components\FileUpload::make('csv_file')
                    ->label('CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->required()
                    ->helperText('Expected columns: ' . implode(', ', $this->importColumns())),
            ])
            ->action(function (array $data): void {
                $this->processImport($data['csv_file']);
            });
    }

    /**
     * Process the uploaded CSV.
     */
    protected function processImport(string $filePath): void
    {
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            Notification::make()->title('File not found')->danger()->send();
            return;
        }

        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            Notification::make()->title('Cannot open file')->danger()->send();
            return;
        }

        // Read headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            Notification::make()->title('Empty file or invalid CSV')->danger()->send();
            return;
        }

        // Normalise headers
        $headers = array_map(fn($h) => strtolower(trim(str_replace([' ', '-'], '_', $h))), $headers);
        $allowedColumns = $this->importColumns();
        $rules = $this->importRules();
        $defaults = $this->importDefaults();

        $imported = 0;
        $errors = [];
        $rowNum = 1;
        $model = $this->importModel();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                if (count($row) !== count($headers)) {
                    $errors[] = "Row {$rowNum}: column count mismatch";
                    continue;
                }

                $rowData = array_combine($headers, $row);

                // Only keep allowed columns
                $rowData = array_intersect_key($rowData, array_flip($allowedColumns));

                // Validate if rules exist
                if (!empty($rules)) {
                    $validator = Validator::make($rowData, $rules);
                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNum}: " . $validator->errors()->first();
                        continue;
                    }
                }

                // Clean empty strings to null
                $rowData = array_map(fn($v) => $v === '' ? null : $v, $rowData);

                // Merge defaults
                $rowData = array_merge($defaults, $rowData);

                $model::create($rowData);
                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Notification::make()
                ->title('Import failed')
                ->body("Error at row {$rowNum}: " . $e->getMessage())
                ->danger()
                ->send();
            fclose($handle);
            @unlink($fullPath);
            return;
        }

        fclose($handle);
        @unlink($fullPath); // Clean up

        $message = "✅ {$imported} rows imported successfully.";
        if (!empty($errors)) {
            $message .= ' ⚠ ' . count($errors) . ' rows skipped: ' . implode('; ', array_slice($errors, 0, 3));
        }

        Notification::make()
            ->title('Import Complete')
            ->body($message)
            ->success()
            ->send();
    }
}
