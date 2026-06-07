<?php

namespace App\Http\Controllers;

use App\Exports\SitesExport;
use App\Exports\IncidentsExport;
use App\Exports\AlertesExport;
use App\Exports\RapportsExport;
use App\Services\AuditService;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function sites()
    {
        $filename = 'sites_' . now()->format('Ymd_His') . '.xlsx';
        $this->audit('sites', $filename);
        return Excel::download(new SitesExport(), $filename);
    }

    public function incidents()
    {
        $filename = 'incidents_' . now()->format('Ymd_His') . '.xlsx';
        $this->audit('incidents', $filename);
        return Excel::download(new IncidentsExport(), $filename);
    }

    public function alertes()
    {
        $filename = 'alertes_' . now()->format('Ymd_His') . '.xlsx';
        $this->audit('alertes', $filename);
        return Excel::download(new AlertesExport(), $filename);
    }

    public function rapports()
    {
        $filename = 'rapports_' . now()->format('Ymd_His') . '.xlsx';
        $this->audit('rapports', $filename);
        return Excel::download(new RapportsExport(), $filename);
    }

    private function audit(string $type, string $filename): void
    {
        try {
            AuditService::log(
                action:      'export_xlsx',
                category:    'export',
                description: "Export Excel des {$type} → {$filename}",
                newValues:   ['type' => $type, 'filename' => $filename]
            );
        } catch (\Throwable $e) {
            \Log::warning('[AUDIT] export_xlsx skipped: ' . $e->getMessage());
        }
    }
}