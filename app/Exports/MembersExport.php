<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class MembersExport
{
    public function __construct(private readonly Collection $members)
    {
    }

    public function rows(): array
    {
        $header = [
            'N° Socio (ID)',
            'Nombre',
            'Apellido',
            'Categoría',
            'Documento',
            'Teléfono',
            'Ciudad',
            'Dirección',
            '¿Está al día?',
            'Meses adeudados',
        ];

        $rows = $this->members
            ->map(function ($member) {
                return [
                    $member->id,
                    $member->first_name,
                    $member->last_name,
                    $member->category,
                    $member->document_number,
                    $member->phone,
                    $member->city,
                    $member->address,
                    $member->is_up_to_date ? 'Sí' : 'No',
                    collect($member->missing_months)
                        ->map(fn ($month) => \Carbon\Carbon::create()->month($month)->translatedFormat('F'))
                        ->implode(', '),
                ];
            })
            ->all();

        return array_merge([$header], $rows);
    }

    public function toCsv(): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($this->rows() as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return "\xEF\xBB\xBF".$csv;
    }
}