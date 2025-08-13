<?php

namespace App\Helpers;

class IconHelper
{
    public static function getIconNameFromFgLogo($fglogo)
    {
        $iconMapping = [
            1 => 'assignment_ind',
            2 => 'diversity_3',
            3 => 'gavel',
            4 => 'payment',
            'nome' => 'name',
        ];

        return $iconMapping[$fglogo] ?? '';
    }
}
