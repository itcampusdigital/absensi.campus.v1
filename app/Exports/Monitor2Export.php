<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class Monitor2Export implements FromView
{
    function __construct($data,$date_array,$date_convert_array) {
        $this->data = $data;
        $this->date_array = $date_array; 
        $this->date_convert_array = $date_convert_array; 
    }
    public function view(): View
    {
        return view('admin.summary.attendance.monitor-export2', 
        [
            'ceks' => $this->data, 
            'days' => $this->date_array,
            'dates_convert' => $this->date_convert_array,
        ]);
    } 
}
