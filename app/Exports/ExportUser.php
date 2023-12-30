<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ExportUser implements ShouldAutoSize, FromView
{
    private $userExport;

    function __construct($userExport) {
        $this->userExport = $userExport;
    }

    public function view(): View
    {
        return view('admin.user.export', ['users' => $this->userExport]);
    }    

    // public function headings(): array
    // {
    //     return ["Nama", "Nomor Hp", "Tanggal Bergabung"];
    // }
}
