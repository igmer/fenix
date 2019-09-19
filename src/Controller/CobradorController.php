<?php

namespace App\Controller;

use App\Entity\Cobrador;
use App\Entity\RegistroPago;
use App\Entity\User;
use App\Form\CobradorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CobradorController extends Controller
{
    /**
     * @Route("/crear_cobrador", name="cobrador")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $cobrador = new Cobrador();
        $form = $this->CreateForm(CobradorType::class,$cobrador);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $cobrador->setUser($this->getUser());
            $cobrador->setCedula($form->get('cedula')->getData());
            $encript = $this->random_str(32);
            $cobrador->setRuta($encript);
            $username = $form->get('username')->getData();
            $email = $form->get('email')->getData();
            $this->register($email,$username,$form->get('cedula')->getData());
            $em->persist($cobrador);
            $em->flush();
            $this->redirectToRoute('cobrador');
            unset($cobrador);
            unset($form);
            $cobrador = new Cobrador();
            $form = $this->CreateForm(CobradorType::class,$cobrador);

        }
        $query =$em->getRepository(Cobrador::class)->findBy(array('user'=>$user));
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
            return $this->render('cobrador/create_cobrador.html.twig', array('form'=>$form->createView(), 'pagination'=>$pagination));
    }


    /**
     * @Route("/cobros_cobrador/{id_cobrador}", name="cobros_cobrador")
     */
    public function cobros_cobrador(Request $request, $id_cobrador){
        $em = $this->getDoctrine()->getManager();
        $cobros = $em->getRepository(RegistroPago::class)->findBy(array('cobradorId'=>$id_cobrador));

        return $this->render('cobrador/cobros_cobrador.html.twig', array('Cobros'=>$cobros, 'fechaPago' => 'ASC4'));
    }

    /**
     * @Route("/detalle_cobrador/{ruta_cobrador}", name="detalle_cobrador")
     */
    public function cobrador_detail(Request $request, $ruta_cobrador){
        $em = $this->getDoctrine()->getManager();
        $cobrador = $em->getRepository(Cobrador::class)->findOneBy(array('ruta'=>$ruta_cobrador));
        $user = $em->getRepository(User::class)->findOneBy(array('email'=>$cobrador->getEmail()));
        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, array('data'=>$cobrador->getNombre()))
            ->add('direccion', TextType::class, array('data'=>$cobrador->getDireccion()))
            ->add('barrio', TextType::class, array('data'=>$cobrador->getBarrio()))
            ->add('fijo', TextType::class, array('data'=>$cobrador->getFijo()))
            ->add('celular', TextType::class, array('data'=>$cobrador->getCelular()))
            ->add('Guardar', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $cobrador->setNombre($form->get('nombre')->getData());
            $cobrador->setDireccion($form->get('direccion')->getData());
            $cobrador->setBarrio($form->get('barrio')->getData());
            $cobrador->setFijo($form->get('fijo')->getData());
            $cobrador->setCelular($form->get('celular')->getData());
            $em->flush();
            $this->redirectToRoute('detalle_cobrador',array('ruta_cobrador'=>$cobrador->getRuta()));
        }
        $prestamosCobrador = $cobrador->getPrestamo();
        $CobroDía = 0;
        $fecha = new \DateTime();

        foreach ($prestamosCobrador as $prestamo){
            $cuotas = $prestamo->getPagoCuota();
            foreach ($cuotas as $cuota){
                if(($cuota->getEstado() =='Saldado') && date_diff($cuota->getFechaCancelacion(), $fecha)->days == 0){
                    $CobroDía += $cuota->getSaldo();
                }
            }
        }
        return $this->render('cobrador/detalle_cobrador.html.twig', array(
            'cobrador'=>$cobrador,
            'user'=>$user,
            'form'=>$form->createView(),
            'prestamosCobrador'=>$prestamosCobrador,
            'CobroDia'=>$CobroDía
        ));

    }





    private function register($email,$username,$password){
        $userManager = $this->get('fos_user.user_manager');
        $email_exist = $userManager->findUserByEmail($email);
        if($email_exist){
            return false;
        }
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1);
        $user->setPlainPassword($password);
        $user->setUserEstadp(User::USUARIOCOBRADOR);
        $user->addRole('ROLE_COBRADOR');
        $userManager->updateUser($user);
        return true;
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
