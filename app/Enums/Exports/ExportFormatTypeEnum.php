<?php

namespace App\Enum\Exports;

use App\Enums\EnumUtils;

enum ExportFormatTypeEnum: string
{
    use EnumUtils;

    case EXCEL = 'excel';
    case PDF = 'pdf';
}
