<?php

namespace AppBundle\Controller\Api;

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
    /**
     * @Route("/validate/{formType}", name="api_form_validate", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function validate(Request $request, Serializer $serializer, string $formType): Response
    {
        if (!class_exists($formType)) {
            throw $this->createNotFoundException('Form not found');
        }

        $form = $this->createForm($formType, null, ['csrf_protection' => false]);

        $form->submit($params = $request->request->all()[$form->getConfig()->getName()], false)->isValid();

        return new Response($serializer->serialize(
            $form->getErrors(true, false),
            'json'
        ));
    }
}
