<?php

namespace App\Controller;

use App\Entity\Order;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\PluginController\EntityPluginController;
use JMS\Payment\CoreBundle\PluginController\PluginController;
use JMS\Payment\CoreBundle\PluginController\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OrdersController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return new Response('<html><body>hi</body></html>');
    }

    /**
     * @Route("/new/{amount}")
     * @return Response
     */
    public function newAction($amount)
    {
        $em = $this->getDoctrine()->getManager();

        $order = new Order($amount);
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('app_orders_show', [
            'orderId' => $order->getId(),
        ]);
    }

    /**
     * @Route("/{orderId}/show")
     * @return Response
     */
    public function showAction($orderId, Request $request, PluginController $ppc)
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->find($orderId);

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount'   => $order->getAmount(),
            'currency' => 'EUR',
            'predefined_data' => [
                'paypal_express_checkout' => [
                    'return_url' => 'https://example.com/return-url',
                    'cancel_url' => 'https://example.com/cancel-url',
                    'useraction' => 'commit',
                ],
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ppc->createPaymentInstruction($instruction = $form->getData());

            $order->setPaymentInstruction($instruction);

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush($order);

            return $this->redirectToRoute('app_orders_paymentcreate', [
                'orderId' => $order->getId(),
            ]);
        }

        return $this->render('Orders/show.html.twig', [
            'order' => $order,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/{orderId}/payment/create")
     */
    public function paymentCreateAction($orderId, PluginController $ppc)
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->find($orderId);

        $payment = $this->createPayment($order, $ppc);

        $result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());

        if ($result->getStatus() === Result::STATUS_SUCCESS) {
            return $this->redirectToRoute('app_orders_paymentcomplete', [
                'orderId' => $order->getId(),
            ]);
        }

        throw $result->getPluginException();

        // In a real-world application you wouldn't throw the exception. You would,
        // for example, redirect to the showAction with a flash message informing
        // the user that the payment was not successful.
    }

    private function createPayment(Order $order, PluginController $ppc)
    {
        $instruction = $order->getPaymentInstruction();
        $pendingTransaction = $instruction->getPendingTransaction();

        if ($pendingTransaction !== null) {
            return $pendingTransaction->getPayment();
        }

        $amount = $instruction->getAmount() - $instruction->getDepositedAmount();

        return $ppc->createPayment($instruction->getId(), $amount);
    }
}
