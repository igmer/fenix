<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Cobrador;
use App\Entity\Prestamo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClienteController extends Controller
{
    /**
     * @Route("/cliente/", name="cliente")
     */
    public function index(Request $request)
    {
        $idUsuarioLogueado = $this->getUser()->getId();
        $cliente = new Cliente();
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Cliente::class)->BuscarClientesPorUsuario($idUsuarioLogueado);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );
        $form = $this->createFormBuilder($cliente)
            ->add('cedula', TextType::class)
            ->add('nombre', TextType::class)
            ->add('nota', TextareaType::class)
            ->add('direccion', TextType::class)
            ->add('barrio', TextType::class)
            ->add('fijo', TextType::class)
            ->add('celular', TextType::class)
            ->add('Guardar', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $encript = $this->random_str(32);
            $cliente->setRuta($encript);
            $cliente->setCedula($form->get('cedula')->getData());
            //$cliente->setCedula($cliente->getId());
            $cliente->setUser($this->getUser());
            $em->persist($cliente);
            $em->flush();
            return $this->redirectToRoute('cliente');
        }
        return $this->render('cliente/cliente_create_view.html.twig', array("form" => $form->createView(), 'pagination' => $pagination));
    }

    /**
     * @Route("/ajax_search_id/", options = { "expose" = true }, name="ajax_search_id")
     */
    public function search_id(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new Exception("Error");
        }
        $exist = false;
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('cedula' => $data['id']));
        if ($cliente) {
            $exist = true;
            $url = $this->generateUrl('cliente_detail', array('ruta' => $cliente->getRuta()));
            $respuestaJson = Array("Validacion" => $exist, "nombre" => $cliente->getNombre(), "url" => $url);
            $response = new JsonResponse($respuestaJson);
            return $response;
        } else {
            $respuestaJson = Array("Validacion" => $exist);
            $response = new JsonResponse($respuestaJson);
            return $response;
        }
    }

    /**
     * @Route("/search_by_name/", options = { "expose" = true }, name="search_by_name")
     */
    public function searrchByName(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $em = $this->getDoctrine()->getManager();
            $nombre = $request->request->get('text');
            //$clientes = $em->getRepository(Cliente::class)->BuscarPorNombre($data['text'], $this->getUser()->getId());
            $clientes = $em->getRepository(Cliente::class)->BuscarPorNombre($nombre, $this->getUser()->getId());
            if ($clientes) {
                $clientesArray = array();
                $array = array("name" => $clientes[0]->getNombre());
                for ($i = 0; $i <= count($clientes) - 1; $i++) {
                    $temp_array = array("name" => $clientes[$i]->getNombre(), "ruta" => $clientes[$i]->getRuta());
                    $clientesArray[] = $temp_array;
                }
                $response = new JsonResponse($clientesArray);
                return $response;
            } else {
                $response = new JsonResponse(array("name" => "Not found", "ruta" => "null"));
                return $response;
            }
        }
    }


    /**
     * @Route("/cliente/{ruta}", name="cliente_detail")
     */
    public function cliente_detail(Request $request, $ruta)
    {
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('ruta' => $ruta));
        $usuario = $this->getUser();
        $cobrador= $em->getRepository(Cobrador::class)->findOneBy(array('email'=>$usuario->getEmail()));
        if ($usuario->getUserEstadp() == 2) {
            $prestamos = $em->getRepository(Prestamo::class)->findBy(array('cliente' => $cliente, 'alive' => 0, 'cobrador' => $cobrador));

        } else {
            $prestamos = $em->getRepository(Prestamo::class)->findBy(array('cliente' => $cliente, 'alive' => 0));
        }
        $form = $this->createFormBuilder($cliente)
            ->add('cedula', IntegerType::class, array('data' => $cliente->getCedula()))
            ->add('nombre', TextType::class, array('data' => $cliente->getNombre()))
            ->add('nota', TextareaType::class, array('data' => $cliente->getNota()))
            ->add('direccion', TextType::class, array('data' => $cliente->getDireccion()))
            ->add('barrio', TextType::class, array('data' => $cliente->getBarrio()))
            ->add('fijo', TextType::class, array('data' => $cliente->getFijo()))
            ->add('celular', TextType::class, array('data' => $cliente->getCelular()))
            ->add('Guardar', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cliente->setCelular($form->get('cedula')->getData());
            $cliente->setNombre($form->get('nombre')->getData());
            $cliente->setNota($form->get('nota')->getData());
            $cliente->setDireccion($form->get('direccion')->getData());
            $cliente->setBarrio($form->get('barrio')->getData());
            $cliente->setFijo($form->get('fijo')->getData());
            $cliente->setCelular($form->get('celular')->getData());
            $em->flush();
            return $this->redirectToRoute('cliente_detail', array("ruta" => $cliente->getRuta()));
        }
        return $this->render('cliente/cliente_detail.html.twig', array('cliente' => $cliente, "form" => $form->createView(), 'prestamos' => $prestamos));
    }


    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }


}
