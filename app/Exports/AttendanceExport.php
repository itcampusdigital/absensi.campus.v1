<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements ShouldAutoSize, FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($attendances) {
        $this->attendances = $attendances;
    }

    public function view(): View
    {
        return view('admin.attendances.export', ['attendances' => $this->attendances]);
    }    
}
