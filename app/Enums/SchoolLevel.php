<?php

namespace App\Enums;

enum SchoolLevel: string
{
    case KINDERGARTEN = 'kindergarten';
    case ELEMENTARY = 'elementary';
    case MIDDLE_SCHOOL = 'middle_school';
    case HIGH_SCHOOL = 'high_school';
    case VOCATIONAL_SCHOOL = 'vocational_school';
    case UNIVERSITY = 'university';
    case NON_FORMAL = 'non_formal';
}
