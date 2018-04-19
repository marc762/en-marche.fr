<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\AdherentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/adherents")
 * @Security("is_granted('ROLE_REFERENT')")
 */
class AdherentsController extends Controller
{
    /**
     * @Route("/count", name="app_adherents_count")
     * @Method("GET")
     */
    public function adherentsCountAction(AdherentRepository $adherentRepository): Response
    {
        $count = $adherentRepository->countByGender();

        array_walk($count, function (&$item) {
            $item = (int) $item['count'];
        });

        $total = array_sum($count);

        array_walk($count, function (&$item) use ($total) {
            $item = $this->calculatePercentage($item, $total);
        });

        $count['total'] = $total;

        return new JsonResponse($count);
    }

    /**
     * @Route("/count_for_referent_managed_area", name="app_adherents_count_for_referent_managed_area")
     * @Method("GET")
     */
    public function adherentsCountForReferentManagedAreaAction(AdherentRepository $adherentRepository): Response
    {
        $count = $adherentRepository->countByGenderManagedBy($this->getUser());

        array_walk($count, function (&$item) {
            $item = (int) $item['count'];
        });

        $total = array_sum($count);

        array_walk($count, function (&$item) use ($total) {
            $item = $this->calculatePercentage($item, $total);
        });

        $count['total'] = $total;

        return new JsonResponse($count);
    }

    private function calculatePercentage(int $nb, int $total): int
    {
        return round($nb / $total * 100);
    }
}
