<?php

namespace App\Exports;

use App\Models\Alerte;
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

class AlertesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, Responsable
{
    use Exportable;

    private string $fileName = 'alertes_monitorpro.xlsx';
    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    public function collection()
    {
        $user = auth()->user();
       $query = Alerte::with('incident.site')->latest('created_at');

        if ($user->role === 'client') {
            $query->whereHas('incident.site', fn($q) => $q->where('user_id', $user->id));
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Site', 'Type', 'Sévérité', 'Message', 'Envoyée à', 'Date'];
    }

    public function map($alerte): array
{
    return [
        $alerte->id,
        $alerte->incident?->site?->client_name ?? 'Site supprimé',
        ucfirst($alerte->type ?? 'alerte'),
        ucfirst($alerte->severite ?? $alerte->niveau ?? 'info'),
        $alerte->message ?? '—',
        $alerte->email_to ?? '—',
        optional($alerte->created_at)->format('d/m/Y H:i'),
    ];
}

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 26, 'C' => 14, 'D' => 14, 'E' => 50, 'F' => 28, 'G' => 18];
    }

    public function title(): string
    {
        return 'Alertes';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4A857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D4D8DD']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $sev = strtolower($sheet->getCell("D{$row}")->getValue());
            if (in_array($sev, ['critique', 'danger', 'high'])) {
                $sheet->getStyle("D{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                    'font' => ['bold' => true, 'color' => ['rgb' => '991B1B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            } elseif (in_array($sev, ['avertissement', 'warning', 'medium'])) {
                $sheet->getStyle("D{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                    'font' => ['bold' => true, 'color' => ['rgb' => '92400E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
                $sheet->getStyle("E{$row}:G{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }
        }

        $sheet->freezePane('A2');
        return [];
    }
}