<?php

namespace Modules\Beneficiary\Enums;

enum EducationStatus: string
{
    case CURRENTLY_ENROLLED = 'currently_enrolled';
    case ENROLLED = 'enrolled';
    case NOT_ENROLLED = 'not_enrolled';
    case GRADUATED = 'graduated';
    case TRANSFERRED = 'transferred';
    case DROPPED_OUT = 'dropped_out';
}
