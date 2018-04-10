<?php

namespace AppBundle\Controller\Api;

use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/form")
 */
class FormController extends Controller
{
    /**
     * @Route("/validate/{formType}", name="api_form_validate")
     * @Method("POST")
     */
    public function validate(Request $request, Serializer $serializer, string $formType): Response
    {
        $form = $this->createForm($formType, null, ['csrf_protection' => false]);

        $form->submit($params = $request->request->all()[$form->getConfig()->getName()], false)->isValid();
        $errors = $form->getErrors(true);

        return new Response($serializer->serialize($errors, 'json'));
    }

    /**
     * @Route("/validate/{formType}/fields", name="api_form_validate_fields")
     * @Method("POST")
     */
    public function validateField(Request $request, ValidatorInterface $validator, Serializer $serializer, string $formType): Response
    {
        $form = $this->createForm($formType, null, ['csrf_protection' => false]);

        $form->submit($params = $request->request->all()[$form->getConfig()->getName()], false);

        $data = $form->getData();

        $errors = new ConstraintViolationList();
        foreach (array_keys($params) as $param) {
            $errors->addAll($validator->validateProperty($data, $param, $form->getConfig()->getOption('validation_groups')));
        }

        return new Response($serializer->serialize([$form->getConfig()->getName() => $errors], 'json'));
    }
}
