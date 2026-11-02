<?php

return [
    /*
    | Official rule printed on the Junior APER form (p.12), applied to all
    | non-teaching staff: a staff member who does not obtain at least this
    | percentage in punctuality during the year is NOT eligible for
    | promotion or the annual salary increment.
    |
    | Interpretation used (change here if Registry decides otherwise):
    |   - HOD's punctuality grade (1–5) as a percentage of 5, and
    |   - for Junior staff, the Panel's "Punctuality & Regularity" score
    |     (out of 10) when it has been entered.
    */
    'punctuality_min_percent' => 70,

    /*
    | Junior Non-Teaching APER is for CONTISS 01 – 05 (official form title).
    */
    'junior_max_contiss_level' => 5,

    /*
    | Retirement ages used for "Date of Mandatory Retirement".
    */
    'retirement_age' => ['academic' => 70, 'non_teaching' => 65],
];
