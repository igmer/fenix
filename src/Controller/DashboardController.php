<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Cobrador;
use App\Entity\PagoCuota;
use App\Entity\Prestamo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $currentUser = $this->getUser();
        if ($currentUser) {
            if ($currentUser->getUserEstadp()==0 || $currentUser->getUserEstadp()==1) {
                $response = $this->forward('App\Controller\DashboardController::dashboard');
                return $response;
            } else {
                $response = $this->forward('App\Controller\DashboardController::dashBoardCobrador');
                return $response;
            }
        } else {
            $router = $this->container->get('router');
            return new RedirectResponse($router->generate('Ingresar'), 307);
        }

    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        $fecha = new \DateTime();
        $fechaConsulta = new \DateTime();
        $fechaFin = $fechaConsulta->modify('+1 month');
        $mesActual = $fecha->format('Y-m');
        $mesFin = $fechaFin->format('Y-m');
        $clientes = $em->getRepository(Cliente::class)->ContarClientesPorUsuario($usuario->getId());
        $cobradores = $em->getRepository(Cobrador::class)->ContarCobradoresPorUsuario($usuario->getId());
        $prestamos = $em->getRepository(Prestamo::class)->BuscarPrestamosPorUsuario($clientes);
        $cuotas = $em->getRepository(PagoCuota::class)->BuscarCuotas($prestamos, $mesActual, $mesFin);
        //$prestamosCobradores = $em->getRepository(Prestamo::class)->BuscarPrestamosCobradores($cobradores);
//
//        foreach ($cobradores as $cobrador) {
//            $cobrosDiarios = [];
//            foreach ($prestamosCobradores as $prestamoCobrador){
//                if($prestamoCobrador->getEstado()!='Saldado' and $prestamoCobrador->getCobrador()==$cobrador){
//
//                }
//            }
//        }
        foreach ($cuotas as $cuota) {
            if (
                (date_diff($cuota->getFechaPago(), $fecha)->days - 1 >= 3)
                and (strcmp($cuota->getEstado(), 'En mora') !== 0)
                and ($cuota->getFechaPago() < $fecha)
            ) {
                //$Moras[]=$cuota;
                $cuota->setEstado('En mora');
                $cuota->AplicarMora();
                $em->flush();

            }
        }
        return $this->render('dashboard/dashboard.html.twig', array(
            'cantidadClientes' => count($clientes),
            'cantidadCobradores' => count($cobradores),
            'cantidadPrestamos' => count($prestamos),
            'prestamos' => $prestamos,
            'cuotas' => $cuotas,
            'fecha' => $fecha,
            //'prestamosCobradores' => $prestamosCobradores
        ));
    }

    /**
     * @Route("/dashboardCobrador", name="dashboardCobrador")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashBoardCobrador()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        $cobrador = $em->getRepository(Cobrador::class)->ObtenerCobradorPorEmail($usuario->getEmail());
        $prestamos = $em->getRepository(Prestamo::class)->BuscarPrestamosCobradorLogueado($cobrador);
        $fecha = new \DateTime();
        $fechaConsulta = new \DateTime();
        $fechaFin = $fechaConsulta->modify('+1 month');
        $mesActual = $fecha->format('Y-m');
        $mesFin = $fechaFin->format('Y-m');
        $cuotas = $em->getRepository(PagoCuota::class)->BuscarCuotas($prestamos, $mesActual, $mesFin);
        foreach ($cuotas as $cuota) {
            if (
                (date_diff($cuota->getFechaPago(), $fecha)->days - 1 >= 3)
                and (strcmp($cuota->getEstado(), 'En mora') !== 0)
                and ($cuota->getFechaPago() < $fecha)
            ) {
                //$Moras[]=$cuota;
                $cuota->setEstado('En mora');
                $cuota->AplicarMora();
                $em->flush();

            }
        }
        return $this->render('dashboard/dashboardCobrador.html.twig', array(
            'prestamos' => $prestamos,
            'cuotas' => $cuotas,
            'fecha' => $fecha
        ));
    }


    /**
     * @Route("/ClientesDahsboard", options = { "expose" = true }, name="ClientesDahsboard")
     */
    public function BuscarClientes(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $texto = $request->request->get('text');
            $customers = $em->getRepository(Cliente::class)->BuscarPorParametro($texto);
            if ($customers) {
                return $this->redirectToRoute('index');
            }
            $response = new JsonResponse(array('Validation' => "No encontro nada"));
            return $response;
        }
    }


    /**
     * @Route("/ClientesDahsboardEncontrados/{texto}", options = { "expose" = true }, name="ClientesDahsboardEncontrados")
     */
    public function clientesEncontrados($texto)
    {
        $em = $this->getDoctrine()->getManager();
        $clientes = $em->getRepository(Cliente::class)->BuscarPorId($texto);
        if ((count($clientes)) == 0) {
            $clientes = $em->getRepository(Cliente::class)->BuscarPorNombre($texto);
        }
        return $this->render('cliente/clientes_encontrados.html.twig', array('clientes' => $clientes));
    }

}
