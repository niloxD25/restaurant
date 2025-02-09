<?php
namespace App\Enum;

enum CommandeStatus: string
{
    case EN_COURS = 'en cours';
    case EN_ATTENTE = 'en attente';
    case FINI = 'fini';
    case LIVRER = 'livrer';
}
