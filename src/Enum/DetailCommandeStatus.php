<?php
namespace App\Enum;

enum DetailCommandeStatus: string
{
    case EN_COURS = 'en cours';
    case EN_CUISSON = 'en cuisson';
    case FINI = 'fini';
}
