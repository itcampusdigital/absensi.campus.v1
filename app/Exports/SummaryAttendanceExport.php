<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryAttendanceExport implements ShouldAutoSize, FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($users) {
        $this->users = $users;
    }

    public function view(): View
    {
        return view('admin.summary.attendance.export', ['users' => $this->users]);
    } 
}
