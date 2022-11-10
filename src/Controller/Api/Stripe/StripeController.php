<?php

namespace App\Controller\Api\Stripe;

use Svg\Tag\Rect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/stripe", name="api_stripe")]
 */
class StripeController extends AbstractController
{
    /**
     * @Route("/create-customer", name="_create_customer", methods="POST")
     */
    public function createCustomer(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $stripe = new \Stripe\StripeClient([
            'api_key' => $this->getParameter('app.stripe_secret_key'),
            'stripe_version' => '2022-08-01'
        ]);

        $customer = $stripe->customers->create([
            'email' => $data['email']
        ]);

        return $this->json(['customer' => $customer]);
    }

    /**
     * @Route("/update-payment-intent", name="_update_payment_intent", methods="POST")
     */
    public function updatePaymentIntent(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $stripe = new \Stripe\StripeClient([
            'api_key' => $this->getParameter('app.stripe_secret_key'),
            'stripe_version' => '2022-08-01'
        ]);

        $paymentIntent = $stripe->paymentIntents->update(
            $data['paymentIntentId'],
            [ 'payment_method' => $data['paymentMethod'] ]
        );
        
        return $this->json(['clientSecret' => $paymentIntent->client_secret]);
    }

    /**
     * @Route("/create-payment-intent", name="_create_payment_intent", methods="POST")
     */
    public function createPaymentIntent(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $amount = round($data['amount'] * 100, 0);

        $stripe = new \Stripe\StripeClient([
            'api_key' => $this->getParameter('app.stripe_secret_key'),
            'stripe_version' => '2022-08-01'
        ]);

        $paymentMethods = $stripe->customers->allPaymentMethods(
            $data['customer'],
            [ 'type' => 'card' ]
        );

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'EUR',
            'description' => 'La Tournichette',
            'automatic_payment_methods' => [
                'enabled' => true
            ],
            'customer' => $data['customer'],
            'setup_future_usage' => 'off_session'
        ]);

        return $this->json([
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * @Route("/charge-existing-card", name="_charge_existing_card", methods="POST")
     */
    public function chargeExistingCard(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $amount = round($data['amount'] * 100, 0);

        $stripe = new \Stripe\StripeClient([
            'api_key' => $this->getParameter('app.stripe_secret_key'),
            'stripe_version' => '2022-08-01'
        ]);

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'EUR',
                'description' => 'La Tournichette',
                'payment_method' => $data['paymentMethod'],
                'customer' => $data['paymentCustomerId'],
                'off_session' => true,
                'confirm' => true
            ]);

            return $this->json([
                'succeeded' => true,
                'paymentIntentId' => $paymentIntent->id
            ]);

        } catch(\Stripe\Exception\CardException $e) {
            if ($e->getError()->code === 'authentication_required') {
                $error = ['error' => 'authentication nécessaire, saisir la carte'];
            } elseif ($e->getError()->code) {
                $error = ['error' => $e->getError()->code];
            } else {
                error_log('une erreur est survenue : ' . $e->getError()->code);
                return $this->json(null);
            }

            return $this->json($error);
        }

    }

    /**
     * @Route("/delete-card'", name="_delete_card", methods="POST")
     */
    public function deleteCard(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $stripe = new \Stripe\StripeClient([
            'api_key' => $this->getParameter('app.stripe_secret_key'),
            'stripe_version' => '2022-08-01'
        ]);

        $paymentMethod = [];
        
        foreach ($data['paymentMethodIdList'] as $currentPaymentMethodId) {
            $paymentMethod[] = $stripe->paymentMethods->detach($currentPaymentMethodId);
        }

        return $this->json($paymentMethod);
    }
}
