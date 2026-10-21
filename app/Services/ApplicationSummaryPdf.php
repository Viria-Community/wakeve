<?php

namespace App\Services;

use App\Models\Company;

class ApplicationSummaryPdf
{
    public function generate(Company $company): string
    {
        $company->loadMissing(['transportModes', 'documents.documentType', 'users']);
        $manager = $company->users->firstWhere('role', 'company_manager');
        $commands = [
            '0.06 0.09 0.16 rg 0 750 595 92 re f',
            $this->text('F2', 22, 48, 797, 'PNMG - Mobilite Gabon', '1 1 1'),
            $this->text('F1', 10, 48, 775, 'Recapitulatif de demande d’enregistrement', '0.82 0.88 1'),
            $this->text('F2', 18, 48, 715, 'Demande soumise avec succes', '0.07 0.28 0.62'),
            $this->text('F1', 10, 48, 694, 'Conservez ce document pour le suivi administratif de votre dossier.'),
            '0.94 0.97 1 rg 48 630 499 45 re f',
            $this->text('F1', 9, 62, 657, 'NUMERO DE REFERENCE', '0.35 0.42 0.53'),
            $this->text('F2', 16, 252, 652, $company->application_reference ?? 'NON ATTRIBUEE', '0.07 0.28 0.62'),
        ];

        $y = 590;
        $this->section($commands, $y, 'Compagnie', [
            ['Raison sociale', $company->legal_name],
            ['Nom commercial', $company->trade_name ?: '-'],
            ['RCCM / NIF', trim(($company->rccm ?: '-').' / '.($company->nif ?: '-'))],
            ['Province', config('geography.'.$company->province.'.name', 'Non renseignée')],
            ['Adresse', trim(($company->address ?: '-').' - '.($company->city ?: '-'))],
            ['Contact', $company->email.' / '.$company->phone],
        ]);

        $y -= 176;
        $this->section($commands, $y, 'Gestionnaire et activite', [
            ['Gestionnaire', $manager?->name ?? '-'],
            ['Email de connexion', $manager?->email ?? '-'],
            ['Mode de transport', ucfirst($company->transportModes->first()?->label ?? '-')],
            ['Statut du dossier', 'En attente de validation administrative'],
        ]);

        $y -= 150;
        $documents = $company->documents->map(fn ($document) => [
            $document->documentType?->label ?? 'Document',
            'Valable jusqu’au '.optional($document->valid_until)->format('d/m/Y'),
        ])->values()->all();
        $this->section($commands, $y, 'Documents transmis', $documents);

        $commands[] = '0.85 0.88 0.92 RG 48 67 m 547 67 l S';
        $commands[] = $this->text('F1', 8, 48, 48, 'Document genere le '.now()->format('d/m/Y a H:i').' - Plateforme Nationale de Mobilite du Gabon', '0.35 0.42 0.53');

        return $this->buildPdf(implode("\n", $commands));
    }

    private function section(array &$commands, float $y, string $title, array $rows): void
    {
        $commands[] = $this->text('F2', 13, 48, $y, $title, '0.06 0.09 0.16');
        $commands[] = '0.2 0.42 0.9 RG 48 '.($y - 8).' m 547 '.($y - 8).' l S';
        $rowY = $y - 28;

        foreach ($rows as [$label, $value]) {
            $commands[] = $this->text('F2', 9, 48, $rowY, $label, '0.35 0.42 0.53');
            $commands[] = $this->text('F1', 9, 190, $rowY, (string) $value);
            $rowY -= 22;
        }
    }

    private function text(string $font, int $size, float $x, float $y, string $value, string $color = '0.06 0.09 0.16'): string
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT', $value) ?: $value;
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);

        return "BT $color rg /$font $size Tf 1 0 0 1 $x $y Tm ($escaped) Tj ET";
    }

    private function buildPdf(string $content): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            '<< /Length '.strlen($content)." >>\nstream\n$content\nendstream",
        ];
        $pdf = "%PDF-1.4\n%PNMG\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n$object\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf.'trailer << /Size '.(count($objects) + 1).' /Root 1 0 R >>'."\nstartxref\n$xref\n%%EOF";
    }
}
