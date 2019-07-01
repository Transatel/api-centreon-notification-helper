<?php

namespace App\Enums;

use App\Core\BasicEnum;

class NagiosPluginErrno extends BasicEnum {
    const OK = 0;
    const WARNING = 1;
    const CRITICAL = 2;
    const UNKNOWN = 3;
    const PENDING = 4;
}