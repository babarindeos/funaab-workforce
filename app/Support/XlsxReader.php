<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Minimal .xlsx reader (no extra Composer package needed). Returns each sheet
 * as rows of cell values. Dates come back as Excel serial numbers; the caller
 * converts the columns it knows are dates (see XlsxReader::toDate).
 */
class XlsxReader
{
    private ZipArchive $zip;
    private array $strings = [];
    /** @var array<string,string> sheet name => path inside the zip */
    private array $sheets = [];

    public function __construct(string $path)
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP "zip" extension is not enabled. In XAMPP, open php.ini, remove the ";" before "extension=zip", then restart Apache.');
        }
        $this->zip = new ZipArchive();
        if ($this->zip->open($path) !== true) {
            throw new RuntimeException('This file could not be opened as an Excel (.xlsx) workbook.');
        }
        $this->loadStrings();
        $this->loadSheets();
    }

    public function sheetNames(): array
    {
        return array_keys($this->sheets);
    }

    /** @return array<int, array<int, mixed>> rows of cells, 0-based, gaps filled with null */
    public function rows(string $sheet): array
    {
        $xml = $this->xml($this->sheets[$sheet] ?? throw new RuntimeException("Sheet '{$sheet}' not found."));
        $rows = [];
        foreach ($xml->sheetData->row ?? [] as $row) {
            $r = (int) $row['r'] - 1;
            $cells = [];
            foreach ($row->c as $c) {
                $col = self::colIndex(preg_replace('/\d+/', '', (string) $c['r']));
                $cells[$col] = $this->value($c);
            }
            if ($cells) {
                $max = max(array_keys($cells));
                $rows[$r] = array_replace(array_fill(0, $max + 1, null), $cells);
            }
        }
        ksort($rows);
        return array_values($rows);
    }

    public static function toDate(mixed $v): ?string
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v) && $v > 59 && $v < 80000) {
            return gmdate('Y-m-d', (int) round(((float) $v - 25569) * 86400));
        }
        $s = trim(str_replace(['Sept.', 'Sept '], ['Sep.', 'Sep '], (string) $v));
        foreach (['m/d/Y', 'n/j/Y', 'd/m/Y', 'Y-m-d', 'Y-m-d H:i:s'] as $f) {
            $d = \DateTime::createFromFormat('!' . $f, $s);
            if ($d && $d->format($f) === $s) return $d->format('Y-m-d');
        }
        $t = strtotime(str_replace('.', '', $s));
        return $t ? date('Y-m-d', $t) : null;
    }

    private function value(\SimpleXMLElement $c): mixed
    {
        $t = (string) $c['t'];
        if ($t === 'inlineStr') return trim($this->text($c->is));
        if (!isset($c->v)) return null;
        $v = (string) $c->v;
        return match ($t) {
            's'     => $this->strings[(int) $v] ?? null,
            'b'     => (bool) $v,
            'str', 'e' => $v,
            default => is_numeric($v) ? $v + 0 : $v,
        };
    }

    private function loadStrings(): void
    {
        if (($raw = $this->zip->getFromName('xl/sharedStrings.xml')) === false) return;
        $xml = simplexml_load_string($raw);
        foreach ($xml->si as $si) {
            $this->strings[] = $this->text($si);
        }
    }

    private function text(?\SimpleXMLElement $node): string
    {
        if ($node === null) return '';
        if (isset($node->t)) return (string) $node->t;
        $s = '';
        foreach ($node->r ?? [] as $run) $s .= (string) $run->t;
        return $s;
    }

    private function loadSheets(): void
    {
        $wb   = $this->xml('xl/workbook.xml');
        $rels = $this->xml('xl/_rels/workbook.xml.rels');
        $targets = [];
        foreach ($rels->Relationship as $rel) {
            $target = ltrim((string) $rel['Target'], '/');
            $targets[(string) $rel['Id']] = str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
        }
        foreach ($wb->sheets->sheet as $sheet) {
            $rid = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            if (isset($targets[$rid])) $this->sheets[(string) $sheet['name']] = $targets[$rid];
        }
    }

    private function xml(string $name): \SimpleXMLElement
    {
        $raw = $this->zip->getFromName($name);
        if ($raw === false) throw new RuntimeException("The workbook is missing {$name}.");
        return simplexml_load_string($raw);
    }

    private static function colIndex(string $letters): int
    {
        $n = 0;
        foreach (str_split(strtoupper($letters)) as $ch) $n = $n * 26 + (ord($ch) - 64);
        return $n - 1;
    }
}
