<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonitorExport implements ShouldAutoSize, FromView
{

    function __construct($monitoring) {
        $this->monitoring = $monitoring;
    }

    public function view(): View
    {
        return view('admin.summary.attendance.monitor-export', ['monitoring' => $this->monitoring]);
    } 
}
