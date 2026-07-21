<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderSummaryReportExport implements FromView, ShouldAutoSize, WithStyles, WithEvents
{
    use Exportable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('file-exports.order-summary-export', [
            'data' => $this->data,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:D2')->getFont()->setBold(true);

        $sheet->getStyle('A2:D2')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'F2F2F2'],
        ]);

        return [
            'A2:D' . $highestRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getStyle('A1:D' . $event->sheet->getHighestRow())
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
