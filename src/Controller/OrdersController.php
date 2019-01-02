<?php

namespace App\Controller;

use App\Entity\Order;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
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
    public function showAction($orderId)
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->find($orderId);

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount'   => $order->getAmount(),
            'currency' => 'EUR',
        ]);

        return $this->render('Orders/show.html.twig', [
            'order' => $order,
            'form'  => $form->createView(),
        ]);
    }

}
