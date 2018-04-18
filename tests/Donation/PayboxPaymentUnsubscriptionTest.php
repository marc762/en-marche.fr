<?php

namespace Tests\AppBundle\Donation;

use AppBundle\Donation\DonationFactory;
use AppBundle\Donation\DonationRequestUtils;
use AppBundle\Donation\PayboxPaymentUnsubscription;
use AppBundle\Exception\PayboxPaymentUnsubscriptionException;
use AppBundle\Mailer\MailerService;
use Lexik\Bundle\PayboxBundle\Paybox\System\Cancellation\Request;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Donation\DonationRequest;
use libphonenumber\PhoneNumber;
use Ramsey\Uuid\Uuid;

class PayboxPaymentUnsubscriptionTest extends WebTestCase
{
    public const DONATION_REQUEST_UUID = 'cfd3c04f-cce0-405d-865f-f5f3a2c1792e';

    public function testUnsubscribError(): void
    {
        $request = $this->createRequestDonation();
        $factory = $this->getContainer()->get(DonationFactory::class);
        $donation = $factory->createFromDonationRequest($request);

        $payboxPaymentUnsubscribtion = $this->createPayboxPaymentUnsubscriptionError();

        $this->expectException(PayboxPaymentUnsubscriptionException::class);
        $this->expectExceptionMessage('Echec de la résiliation. Aucun abonnement résilié');

        $payboxPaymentUnsubscribtion->unsubscribe($donation);
    }

    public function testUnsubscribSuccess(): void
    {
        $request = $this->createRequestDonation();
        $factory = $this->getContainer()->get(DonationFactory::class);
        $donation = $factory->createFromDonationRequest($request);

        $payboxPaymentUnsubscribtion = $this->createPayboxPaymentUnsubscriptionSuccess();

        $payboxPaymentUnsubscribtion->unsubscribe($donation);

        static::assertNotNull($donation->getSubscriptionEndedAt());
    }

    private function createPayboxPaymentUnsubscriptionError(): PayboxPaymentUnsubscription
    {
        $this->createConfiguredMock(Request::class, []);

        return new PayboxPaymentUnsubscription(
            $this->createConfiguredMock(MailerService::class, []),
            $this->createConfiguredMock(Request::class, [
                'cancel' => 'ACQ=NO&ERREUR=9&IDENTIFIANT=2&REFERENCE=refcmd1',
            ]),
            $this->getContainer()->get(DonationRequestUtils::class)
        );
    }

    private function createPayboxPaymentUnsubscriptionSuccess(): PayboxPaymentUnsubscription
    {
        $this->createConfiguredMock(Request::class, []);

        return new PayboxPaymentUnsubscription(
            $this->createConfiguredMock(MailerService::class, []),
            $this->createConfiguredMock(Request::class, [
                'cancel' => 'ACQ=OK&IDENTIFIANT=2&REFERENCE=refcmd1',
            ]),
            $this->getContainer()->get(DonationRequestUtils::class)
        );
    }

    private function createRequestDonation(): DonationRequest
    {
        $uuid = Uuid::fromString(self::DONATION_REQUEST_UUID);
        $phone = new PhoneNumber();
        $phone->setCountryCode('FR');
        $phone->setNationalNumber('0123456789');

        $request = new DonationRequest($uuid, '3.3.3.3');
        $request->firstName = 'Damien';
        $request->lastName = 'DUPONT';
        $request->gender = 'male';
        $request->setAmount(70.0);
        $request->setEmailAddress('m.dupont@example.fr');
        $request->setCountry('FR');
        $request->setPostalCode('69000');
        $request->setCityName('Lyon');
        $request->setAddress('2, Rue de la République');
        $request->setPhone($phone);
        $request->setDuration(0);

        return $request;
    }
}
