<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum UserType: int
{
    use InvokableCases;
    use Names;
    use Options;
    use Values;

    case ADMIN = 1;
    case TEACHER = 2;
    case STUDENT = 3;
    case PARENT = 4;
    case SECURITY = 5;
    case USER = 6;
    case GUEST = 7;
}
