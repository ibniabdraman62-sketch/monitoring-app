<?php

namespace App\Exports;

use App\Models\Incident;
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

class IncidentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, Responsable
{
    use Exportable;

    private string $fileName = 'incidents_monitorpro.xlsx';
    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    public function collection()
    {
        $user = auth()->user();
        $query = Incident::with('site')->latest('started_at');

        if ($user->role === 'client') {
            $query->whereHas('site', fn($q) => $q->where('user_id', $user->id));
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Site', 'URL', 'Type', 'Début', 'Fin', 'Durée', 'Statut'];
    }

    public function map($incident): array
    {
        $end = $incident->resolved_at ?? $incident->ended_at ?? null;
        $duree = $end && $incident->started_at
            ? $incident->started_at->diffForHumans($end, true)
            : '—';

        return [
            $incident->id,
            $incident->site->client_name ?? 'Site supprimé',
            $incident->site->url ?? '—',
            ucfirst($incident->type ?? 'downtime'),
            optional($incident->started_at)->format('d/m/Y H:i'),
            $end ? \Carbon\Carbon::parse($end)->format('d/m/Y H:i') : '—',
            $duree,
            $end ? 'Résolu' : 'En cours',
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 26, 'C' => 32, 'D' => 14, 'E' => 18, 'F' => 18, 'G' => 18, 'H' => 12];
    }

    public function title(): string
    {
        return 'Incidents';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D4D8DD']]],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $statusCell = $sheet->getCell("H{$row}")->getValue();
            $color = $statusCell === 'Résolu' ? 'D1FAE5' : 'FEE2E2';

            $sheet->getStyle("H{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['bold' => true, 'color' => ['rgb' => $statusCell === 'Résolu' ? '065F46' : '991B1B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }
        }

        $sheet->freezePane('A2');
        return [];
    }
}