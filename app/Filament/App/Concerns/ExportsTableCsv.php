<?php

namespace App\Filament\App\Concerns;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Adds a re-usable "Export CSV" toolbar action to any Filament table page.
 *
 * Usage: In your module page, `use ExportsTableCsv;` then inside
 * `->toolbarActions([..., $this->exportCsvAction('tasks', fn() => $query)])`.
 *
 * The callback should return the Eloquent query for the data to export.
 * The second argument lists the columns as [db_column => header_label].
 */
trait ExportsTableCsv
{
    /**
     * @param  string               $filename   Base file name (no extension)
     * @param  callable(): Builder  $queryFn    Returns the query to export (unscoped by pagination)
     * @param  array<string,string> $columns    Associative array: db_column_or_accessor => CSV header label
     * @param  string|null          $label      Button label (default: "Export CSV")
     */
    protected function exportCsvAction(
        string $filename,
        callable $queryFn,
        array $columns,
        ?string $label = null,
    ): Action {
        return Action::make('export_' . $filename)
            ->label($label ?? 'Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () use ($filename, $queryFn, $columns): StreamedResponse {
                $query = $queryFn();
                $records = $query->get();
                $headers = array_values($columns);
                $keys = array_keys($columns);

                $date = now()->format('Y-m-d');
                $csvFilename = "{$filename}_{$date}.csv";

                return response()->streamDownload(function () use ($records, $headers, $keys) {
                    $handle = fopen('php://output', 'w');

                    // BOM for Excel UTF-8 compatibility
                    fwrite($handle, "\xEF\xBB\xBF");

                    // Write header row
                    fputcsv($handle, $headers);

                    // Write data rows
                    foreach ($records as $record) {
                        $row = [];
                        foreach ($keys as $key) {
                            // Support dot-notation (e.g. 'creator.name')
                            $value = data_get($record, $key);

                            // Format dates
                            if ($value instanceof \Carbon\Carbon || $value instanceof \DateTimeInterface) {
                                $value = $value->format('Y-m-d H:i');
                            }

                            // Strip HTML
                            if (is_string($value)) {
                                $value = strip_tags($value);
                            }

                            // Cast non-scalars
                            if (is_array($value) || is_object($value)) {
                                $value = json_encode($value);
                            }

                            $row[] = $value ?? '';
                        }
                        fputcsv($handle, $row);
                    }

                    fclose($handle);
                }, $csvFilename, [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            });
    }
}
