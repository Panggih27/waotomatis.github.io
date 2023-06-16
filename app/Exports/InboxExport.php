<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InboxExport implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting
{
    use Exportable;

    public $inboxes;

    public function __construct($inboxes)
    {
        $this->inboxes = $inboxes;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->inboxes->map(function ($item, $key) {
            return [
                ($key + 1), $item->sender, $item->body, Date::dateTimeToExcel($item->created_at),
            ]; 
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Pengirim',
            'Pesan',
            'Waktu'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 20,            
            'C' => 75,            
            'D' => 25,            
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => "[<=9999999]###-####;###-###-####",
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
