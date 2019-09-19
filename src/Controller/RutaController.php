<?php

namespace App\Controller;

use App\Entity\Prestamo;
use App\Entity\Ruta;
use App\Form\RutaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RutaController extends Controller
{
    /**
     * @Route("/crear_ruta", name="ruta")
     */
    public function index(Request $request)
    {
        $ruta = new Ruta();
        $user = $this->getUser();
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $ruta->setUser($this->getUser());
            $em->persist($ruta);
            $em->flush();
            unset($form);
            unset($ruta);
            $ruta = new Ruta();
            $form = $this->createForm(RutaType::class, $ruta);
            $this->redirectToRoute('ruta');
        }
        $em=$this->getDoctrine()->getManager();
        $query= $em->getRepository(Ruta::class)->findBy(array('user'=>$user));
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('ruta/create_ruta.html.twig', array('form'=>$form->createView(),'pagination' => $pagination));
    }

    /**
     * @Route("/detalle_ruta/{ruta_id}", name="detalle_ruta")
     */
    public function detalleRuta(Request $request, $ruta_id){

        $em= $this->getDoctrine()->getManager();
        $ruta = $em ->getRepository(Ruta::class)->find($ruta_id);
        $prestamos = $em->getRepository(Prestamo::class)->findBy(array('ruta'=>$ruta));
        $form = $this->createFormBuilder()
            ->add('zona_cobro', TextType::class, array('data'=>$ruta->getZonaCobro()))
            ->add('nombre_socio', TextType::class, array('data'=>$ruta->getNombreSocio()))
            ->add('cuota', IntegerType::class, array('data'=>$ruta->getCuota()))
            ->add('Guardar', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ruta->setZonaCobro($form->get('zona_cobro')->getData());
            $ruta->setNombreSocio($form->get('nombre_socio')->getData());
            $ruta->setCuota($form->get('cuota')->getData());
            $em->flush();
            $this->redirectToRoute('detalle_ruta',array('ruta_id'=>$ruta->getId()));
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $prestamos, /* query NOT result */
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('ruta/detail_ruta.html.twig', array('ruta'=>$ruta, 'form'=>$form->createView(), 'prestamos'=>$pagination));
    }



}
