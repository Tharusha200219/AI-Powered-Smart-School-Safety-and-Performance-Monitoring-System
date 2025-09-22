<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;
use ArchTech\Enums\Options;

enum Status: int
{

    use InvokableCases;
    use Values;
    use Names;
    use Options;

    case ACTIVE = 1;
    case INACTIVE = 2;
    case DISABLE = 3;
}
