<?php

namespace App\Exports;

use App\Models\Site;
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

class SitesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, Responsable
{
    use Exportable;

    private string $fileName = 'sites_monitorpro.xlsx';
    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    public function collection()
    {
        $user = auth()->user();
        $query = Site::with('user')->orderBy('client_name');

        if ($user->role === 'client') {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Client', 'URL', 'Email client', 'Statut',
            'Actif', 'Uptime 30j (%)', 'Dernière vérif', 'Créé le',
        ];
    }

    public function map($site): array
    {
        $last = $site->verifications()->latest('checked_at')->first();
        $uptime = $site->verifications()
            ->where('checked_at', '>=', now()->subDays(30))
            ->avg('is_up');

        return [
            $site->id,
            $site->client_name,
            $site->url,
            $site->client_email ?? '—',
            $last ? ($last->is_up ? 'En ligne' : 'Hors ligne') : 'Inconnu',
            $site->is_active ? 'Oui' : 'Non',
            $uptime !== null ? round($uptime * 100, 2) : 100,
            optional($last?->checked_at)->format('d/m/Y H:i') ?? '—',
            optional($site->created_at)->format('d/m/Y'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6, 'B' => 28, 'C' => 38, 'D' => 28, 'E' => 14,
            'F' => 8, 'G' => 14, 'H' => 18, 'I' => 14,
        ];
    }

    public function title(): string
    {
        return 'Sites monitorés';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D4D8DD']]],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }
        }

        $sheet->freezePane('A2');
        return [];
    }
}