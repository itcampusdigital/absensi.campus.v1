<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonitorExport implements ShouldAutoSize, FromView
{

    function __construct($monitoring, $dates,$array_name_workhours,$shift1,$shift2,$shift3,$sisipan) {
        $this->monitoring = $monitoring;
        $this->dates = $dates;
        $this->shift1 = $shift1;
        $this->shift2 = $shift2;
        $this->shift3 = $shift3;
        $this->sisipan = $sisipan;
        $this->array_name_workhours = $array_name_workhours;
    }

    public function view(): View
    {
        return view('admin.summary.attendance.monitor-export', 
        [
            'monitoring' => $this->monitoring, 
            'dates' => $this->dates,
            'workhours' => $this->array_name_workhours,
            'shift1' => $this->shift1,
            'shift2' => $this->shift2,
            'shift3' => $this->shift3,
            'sisipan' => $this->sisipan

        ]);
    } 
}
