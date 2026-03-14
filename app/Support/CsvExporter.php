<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @param  iterable<int, array<int, mixed>>  $rows
     * @param  array<int, string>  $headers
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM for Excel compatibility.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
