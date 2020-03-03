<?php

namespace AppBundle\Controller\EnMarche\MunicipalManagerAttribution;

use AppBundle\Entity\Adherent;
use AppBundle\MunicipalManager\Filter\AssociationCityFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/espace-superviseur-responsables-communaux", name="app_municipal_manager_municipal_manager_supervisor")
 *
 * @Security("is_granted('ROLE_MUNICIPAL_MANAGER_SUPERVISOR')")
 */
class MunicipalManagerSupervisorMunicipalManagerAttributionController extends AbstractMunicipalManagerAttributionController
{
    private const SPACE_NAME = 'municipal_manager_supervisor';

    protected function getSpaceType(): string
    {
        return self::SPACE_NAME;
    }

    protected function createCityFilter(): AssociationCityFilter
    {
        $filter = new AssociationCityFilter();
        $filter->setManagedTags($this->getReferentTags());

        return $filter;
    }

    private function getReferentTags(): array
    {
        /** @var Adherent $referent */
        $referent = $this->getUser();

        return $referent
            ->getMunicipalManagerSupervisorRole()
            ->getReferent()
            ->getManagedArea()
            ->getTags()
            ->toArray()
        ;
    }
}
