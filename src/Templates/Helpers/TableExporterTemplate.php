<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class TableExporterTemplate
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers\Koperasi\Export;

use Illuminate\Support\Collection;

class TableExporter
{
    public function exportToExcel(array $headers, Collection $data, string $title = 'Export Data', array $totals = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Simple CSV export as Excel alternative
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");
        
        // Write headers
        $headerRow = [];
        foreach ($headers as $header) {
            $headerRow[] = $header->alias ?? $header->field ?? $header;
        }
        fputcsv($handle, $headerRow);
        
        // Write data
        foreach ($data as $row) {
            $dataRow = [];
            foreach ($headers as $header) {
                $field = $header->field ?? $header;
                $value = is_object($row) ? ($row->$field ?? '') : ($row[$field] ?? '');
                $dataRow[] = $value;
            }
            fputcsv($handle, $dataRow);
        }
        
        // Add totals if provided
        if (!empty($totals)) {
            fputcsv($handle, []); // Empty row
            foreach ($totals as $label => $value) {
                fputcsv($handle, [$label, $value]);
            }
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    public function exportToPdf(array $headers, Collection $data, string $title = 'Export Data', array $totals = []): \Illuminate\Http\Response
    {
        // Simple HTML to PDF conversion
        $html = $this->generatePrintView($headers, $data, $title, $totals);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $title . '.html"');
    }
    
    public function generatePrintView(array $headers, Collection $data, string $title = 'Export Data', array $totals = []): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .totals { margin-top: 20px; font-weight: bold; }
        .print-date { text-align: right; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="print-date">Dicetak pada: ' . date('d/m/Y H:i:s') . '</div>
    <h1>' . $title . '</h1>
    
    <table>
        <thead>
            <tr>';
        
        foreach ($headers as $header) {
            $html .= '<th>' . ($header->alias ?? $header->field ?? $header) . '</th>';
        }
        
        $html .= '</tr>
        </thead>
        <tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($headers as $header) {
                $field = $header->field ?? $header;
                $value = is_object($row) ? ($row->$field ?? '') : ($row[$field] ?? '');
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>';
    
    if (!empty($totals)) {
        $html .= '<div class="totals">
            <h3>Ringkasan:</h3>';
        foreach ($totals as $label => $value) {
            $html .= '<p>' . $label . ': ' . $value . '</p>';
        }
        $html .= '</div>';
    }
    
    $html .= '
    <script>
        window.onload = function() {
            if (window.location.search.includes("print=1")) {
                window.print();
            }
        }
    </script>
</body>
</html>';
        
        return $html;
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $exporterPath = app_path('Helpers/Koperasi/Export/TableExporter.php');

        if (! file_exists($exporterPath)) {
            // Create Helpers/Koperasi/Export directory if not exists
            $exporterDir = dirname($exporterPath);
            if (! is_dir($exporterDir)) {
                mkdir($exporterDir, 0755, true);
            }

            file_put_contents($exporterPath, self::getTemplate());
        }
    }
}
