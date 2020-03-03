<?php

namespace AppBundle\Controller\EnMarche\MunicipalManagerAttribution;

use AppBundle\Controller\CanaryControllerTrait;
use AppBundle\Form\MunicipalManagerCityListType;
use AppBundle\Form\ReferentCityFilterType;
use AppBundle\MunicipalManager\Filter\AssociationCityFilter;
use AppBundle\MunicipalManager\MunicipalManagerRole\MunicipalManagerAssociationManager;
use AppBundle\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractMunicipalManagerAttributionController extends Controller
{
    use CanaryControllerTrait;

    protected const PAGE_LIMIT = 10;

    /**
     * @Route("/responsables-communaux", name="_attribution_form", methods={"GET", "POST"})
     */
    public function municipalManagerAttributionAction(
        Request $request,
        CityRepository $cityRepository,
        MunicipalManagerAssociationManager $manager
    ): Response {
        $this->disableInProduction();

        $filterForm = $this
            ->createForm(ReferentCityFilterType::class, $filter = $this->createCityFilter())
            ->handleRequest($request)
        ;

        if ($filterForm->isSubmitted() && !$filterForm->isValid()) {
            $filter = $this->createCityFilter();
        }

        $paginator = $cityRepository->findAllForFilter(
            $filter,
            $request->query->getInt('page', 1),
            self::PAGE_LIMIT
        );

        $form = $this
            ->createForm(MunicipalManagerCityListType::class, $manager->getAssociationValueObjectsFromCities(
                iterator_to_array($paginator)
            ))
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->handleUpdate($form->getData());

            $this->addFlash('info', 'Les modifications ont bien été sauvegardées');

            return $this->redirectToRoute(
                'app_assessors_referent_municipal_manager_attribution_form',
                $this->getRouteParams($request)
            );
        }

        return $this->renderTemplate(sprintf('municipal_manager_attribution/index.html.twig', static::getSpaceType()), [
            'cities' => $paginator,
            'form' => $form->createView(),
            'filter_form' => $filterForm->createView(),
            'route_params' => $this->getRouteParams($request),
        ]);
    }

    protected function getRouteParams(Request $request): array
    {
        $params = [];

        if ($request->query->has('f')) {
            $params['f'] = (array) $request->query->get('f');
        }

        if ($request->query->has('page')) {
            $params['page'] = $request->query->getInt('page');
        }

        return $params;
    }

    protected function renderTemplate(string $template, array $parameters = []): Response
    {
        return $this->render($template, array_merge(
            $parameters,
            [
                'base_template' => sprintf('municipal_manager_attribution/_base_%s_space.html.twig', $spaceType = static::getSpaceType()),
                'space_type' => $spaceType,
            ]
        ));
    }

    abstract protected function createCityFilter(): AssociationCityFilter;

    abstract protected function getSpaceType(): string;
}
