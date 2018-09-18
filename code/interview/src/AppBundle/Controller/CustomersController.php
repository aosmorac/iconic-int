<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Collection;

class CustomersController extends Controller
{
    /**
     * @Route("/customers/")
     * @Method("GET")
     */
    public function getAction()
    {
        $collection = new Collection($this->get('database_service'), $this->get('cache_service'));
        $collection->loadCustomers();

        return new JsonResponse($collection->getCustomers());
    }

    /**
     * @Route("/customers/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $customers = json_decode($request->getContent());
        $collection = new Collection($this->get('database_service'), $this->get('cache_service'));
        $origin = $request->getClientIp();

        if ( $collection->addCustomers( $customers, $origin ) ) {
            return new JsonResponse(['status' => 'Customers successfully created']);
        }else {
            return new JsonResponse(['status' => 'No donuts for you'], 400);
        }
    }

    /**
     * @Route("/customers/")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $collection = new Collection($this->get('database_service'), $this->get('cache_service'));
        $origin = $request->getClientIp();
        if ( $collection->deleteCustomers($origin) ) {
            return new JsonResponse(['status' => 'Customers successfully deleted']);
        }else {
            return new JsonResponse(['status' => 'Error deleting'], 400);
        }


    }
}
