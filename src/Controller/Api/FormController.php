<?php

namespace AppBundle\Controller\Api;

use AppBundle\Form\AdherentRegistrationType;
use AppBundle\Form\BecomeAdherentType;
use AppBundle\Form\DonationSubscriptionRequestType;
use AppBundle\Form\UserRegistrationType;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/form")
 */
class FormController extends Controller
{
    // TODO: Replace this by a compiler pass who crawl all FormTypeApiExposeInterface to add key and class in a FormTypeApiExposeManager
    private const FORM_MAP = [
        'become-adherent' => BecomeAdherentType::class,
        'adherent-registration' => AdherentRegistrationType::class,
        'user-registration' => UserRegistrationType::class,
        'donation-subscription' => DonationSubscriptionRequestType::class,
    ];

    /**
     * @Route("/validate/{formType}", name="api_form_validate")
     * @Method("POST")
     */
    public function validate(Request $request, Serializer $serializer, string $formType): Response
    {
        if (!array_key_exists($formType, static::FORM_MAP)) {
            throw $this->createNotFoundException('Form not available');
        }

        $form = $this->createForm(static::FORM_MAP[$formType], null, ['csrf_protection' => false]);

        $form->submit($params = $request->request->all()[$form->getConfig()->getName()], false)->isValid();
        $errors = $form->getErrors(true, false);

        return new Response($serializer->serialize($errors, 'json'));
    }
}
