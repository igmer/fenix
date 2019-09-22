<?php

namespace App\Controller;

use App\Entity\Administrador;
use App\Entity\User;
use App\Form\AdministradorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministradorController extends Controller
{
    /**
     * @Route("/administrador/", name="administrador")
     */

    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $administrador = new Administrador();
        $user = $this->getUser();
        $form = $this->createForm(AdministradorType::class, $administrador);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('Contrasena')->getData();
            $username = $form->get('NombreUsuario')->getData();
            $email = $form->get('email')->getData();
            $administrador->setContrasena($password);
            $administrador->setEmail($email);
            $administrador->setNombreUsuario($username);
            $administrador->setUsuario($user);
            $this->register($email, $username, $password);
            $em->persist($administrador);
            $em->flush();
            return $this->redirectToRoute('administrador');
        }
        $user = $this->getUser();
        $query = $em->getRepository(Administrador::class)->findBy(array('usuario' => $user));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('administrador/crear_administrador.html.twig', array('pagination' => $pagination, 'form' => $form->createView()));

    }


    private function register($email, $username, $password)
    {
        $userManager = $this->get('fos_user.user_manager');
        $email_exist = $userManager->findUserByEmail($email);
        if ($email_exist) {
            return false;
        }
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');
        //$user->setUserEstadp(User::USUARIO);
        $userManager->updateUser($user);
        return true;
    }

}
