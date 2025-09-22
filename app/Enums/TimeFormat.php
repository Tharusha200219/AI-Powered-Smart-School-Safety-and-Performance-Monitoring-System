<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum TimeFormat : string
{
    use InvokableCases;
    use Values;
    use Names;

    case HIS = "H:i:s";
}
