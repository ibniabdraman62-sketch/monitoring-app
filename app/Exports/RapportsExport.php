<?php

namespace App\Exports;

use App\Models\Rapport;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RapportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, Responsable
{
    use Exportable;

    private string $fileName = 'rapports_monitorpro.xlsx';
    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    public function collection()
    {
        $user = auth()->user();
        $query = Rapport::with('site')->latest('generated_at');

        if ($user->role === 'client') {
            $query->whereHas('site', fn($q) => $q->where('user_id', $user->id));
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Site', 'Période début', 'Période fin', 'Uptime (%)', 'Incidents', 'Tps réponse moy. (ms)', 'Généré le'];
    }

    public function map($rapport): array
    {
        return [
            $rapport->id,
            $rapport->site->client_name ?? 'Site supprimé',
            \Carbon\Carbon::parse($rapport->period_start)->format('d/m/Y'),
            \Carbon\Carbon::parse($rapport->period_end)->format('d/m/Y'),
            round($rapport->uptime_pct, 2),
            $rapport->incidents_count,
            round($rapport->avg_response_ms),
            optional($rapport->generated_at)->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 28, 'C' => 14, 'D' => 14, 'E' => 12, 'F' => 12, 'G' => 18, 'H' => 18];
    }

    public function title(): string
    {
        return 'Rapports';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D4D8DD']]],
        ]);

        // Coloration uptime
        for ($row = 2; $row <= $lastRow; $row++) {
            $uptime = (float) $sheet->getCell("E{$row}")->getValue();
            $color = $uptime >= 99 ? '065F46' : ($uptime >= 95 ? 'D97706' : '991B1B');
            $bg    = $uptime >= 99 ? 'D1FAE5' : ($uptime >= 95 ? 'FEF3C7' : 'FEE2E2');

            $sheet->getStyle("E{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'font' => ['bold' => true, 'color' => ['rgb' => $color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
                $sheet->getStyle("F{$row}:H{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }
        }

        $sheet->freezePane('A2');
        return [];
    }
}