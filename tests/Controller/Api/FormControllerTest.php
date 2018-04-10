<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Form\AdherentRegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ApiControllerTestTrait;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 */
class FormControllerTest extends MysqlWebTestCase
{
    use ControllerTestTrait;
    use ApiControllerTestTrait;

    public function testValidate(): void
    {
        $this->client->request(Request::METHOD_POST, '/api/form/validate/'.urlencode(AdherentRegistrationType::class), [
            'adherent_registration' => [
                'firstName' => '123',
                'lastName' => 't',
                'emailAddress' => [
                    'first' => 'toto@too.fr',
                    'second' => 'titi',
                ],
            ],
        ]);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
        $result = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);
        static::assertArrayNotHasKey('errors', $result['form']['children']['firstName']);
        static::assertSame('Votre prénom doit comporter au moins 2 caractères.', $result['form']['children']['lastName']['errors'][0]);
        static::assertSame('Les adresses email ne correspondent pas.', $result['form']['children']['emailAddress']['children']['first']['errors'][0]);
    }

    public function setUp()
    {
        parent::setUp();

        $this->init([]);
    }

    public function tearDown()
    {
        $this->kill();

        parent::tearDown();
    }
}
