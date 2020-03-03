<?php

namespace AppBundle\Entity\ElectedRepresentative;

use MyCLabs\Enum\Enum;

final class MandateTypeEnum extends Enum
{
    public const CITY_COUNCIL = 'conseiller_municipal';
    public const REGIONAL_COUNCIL = 'conseiller_regional';
    public const DEPARTMENTAL_COUNCIL = 'conseiller_departemental';
    public const SENATOR = 'senateur';
    public const DEPUTY = 'depute';
    public const EURO_DEPUTY = 'euro_depute';
    public const EPCI_MEMBER = 'membre_EPCI';
    public const CORSICA_ASSEMBLY_MEMBER = 'membre_assemblee_corse';
}
