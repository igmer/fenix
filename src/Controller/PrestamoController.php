<?php

namespace App\Controller;

use App\Entity\Cobrador;
use App\Entity\PagoCuota;
use App\Entity\Prestamo;
use App\Entity\RegistroPago;
use App\Entity\Ruta;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cliente;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Constraints\DateTime;


class PrestamoController extends Controller
{
    /**
     * @Route("/prestamo/{prestamo_ruta}", options={"expose"=true}, name="prestamo_detail")
     */
    public function prestamodetail($prestamo_ruta)
    {
        $em = $this->getDoctrine()->getManager();
        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $prestamo_ruta));
        $cuotas_prestamo = $prestamo->getPagoCuota();
        if ($prestamo->getPagado() >= floor($prestamo->getTotal())) {
            $prestamo->setEstado("Saldado");
            $em->flush();
        }
        return $this->render('prestamos/prestamos_detail_view.html.twig', array('prestamo' => $prestamo, 'cuotas_prestamos' => $cuotas_prestamo));
    }

    /**
     * @Route("/EliminarPrestamo/{prestamo_ruta}/{ruta_cliente}", options = { "expose" = true }, name="EliminarPrestamo")
     */
    public function EliminarPrestamo($prestamo_ruta, $ruta_cliente)
    {
        $em = $this->getDoctrine()->getManager();
        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $prestamo_ruta));
        $prestamo->setAlive(1);
        $em->flush();
        $response = $this->forward('App\Controller\ClienteController::cliente_detail', array('ruta' => $ruta_cliente));
        return $response;
    }


    /**
     * @Route("/crear_prestamo_cliente/{ruta_cliente}", options = { "expose" = true }, name="crear_prestamo_cliente")
     */
    public function crear_prestamo_cliente($ruta_cliente, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('ruta' => $ruta_cliente));
        $prestamo = new Prestamo();
        $usuario = $this->getUser();
        $cobradores = $em->getRepository(Cobrador::class)->findBy(array('user' => $usuario));
        $rutas = $em->getRepository(Ruta::class)->findBy(array('user' => $usuario));
        $form = $this->createFormBuilder($prestamo)
            ->add('valor_prestamo', IntegerType::class)
            ->add('modo_pago', ChoiceType::class,
                array('choices' => array(
                    'Diario' => 'Diario',
                    'Semanal' => 'Semanal',
                    'Quincenal' => 'Quincenal',
                    'Mensual' => 'Mensual'
                )))
            ->add('dias_pago', IntegerType::class)
            ->add('tasa_interes', IntegerType::class)
            ->add('total_cuotas', IntegerType::class)
            ->add('precio_cuota', TextType::class)
            ->add('total', TextType::class)
            ->add('cobrador', EntityType::class, array(
                'class' => Cobrador::class,
                'choice_label' => 'nombre',
                'choices' => $cobradores
            ))
            ->add('ruta', EntityType::class, array(
                'class' => Ruta::class,
                'choice_label' => 'zona_cobro',
                'choices' => $rutas
            ))
            ->add('EfectuarPrestamo', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this->random_str(16);
            $prestamo->setEstado('Activo');
            $prestamo->setCliente($cliente);
            $prestamo->setUrl("prestamo" . $url);
            $prestamo->setPagado(0);
            $prestamo->setAlive(0);
            $fecha = new \DateTime();
            $fecha_actual = new \DateTime();
            $prestamo->setFechaPrestamo($fecha_actual);
            for ($i = 0; $i < $prestamo->getTotalCuotas(); $i++) {
                switch ($form->get('modo_pago')->getData()) {
                    case 'Diario':
                        $fecha->modify('+1 day');
                        break;
                    case 'Semanal':
                        $fecha->modify('+7 day');
                        break;
                    case 'Quincenal':
                        $fecha->modify('+15 day');
                        break;
                    case 'Mensual':
                        $fecha->modify('+31 day');
                        break;
                }
                $pagocuota = new PagoCuota($prestamo->getPrecioCuota(), $fecha, "Activo", $prestamo);
                $em->persist($pagocuota);
                $em->flush();
            }
            $em->persist($prestamo);
            $flush = $em->flush();
            if ($flush == null) {
                return $this->redirectToRoute('cliente_detail', array('ruta' => $cliente->getRuta()));
            }
        }
        return $this->render('cliente/cliente_prestamos_view.html.twig', array('cliente' => $cliente, 'form' => $form->createView()));
    }


    /**
     * @Route("/pagar_cuota/", options = { "expose" = true }, name="pagar_cuota")
     */
    public function pagar_cuota(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new Exception("Ups! This is not an Ajax call");
        }
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $cuota = $em->getRepository(PagoCuota::class)->find($data['id']);
        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $data['url']));
        $pagado = $prestamo->getPagado();
        $prestamo->setPagado($pagado + $cuota->getSaldo());
        $cuota->setEstado("Saldado");
        $cuota->setFechaCancelacion(new \DateTime());
        $RegistroPago = new RegistroPago($cuota->getFechaCancelacion(), $prestamo->getCobrador()->getCedula(), $cuota->getId(), $cuota->getSaldo());
        $RegistroPago->setAccion("Recibio pago cuota");
        $em->persist($RegistroPago);
        $flush = $em->flush();
        if ($flush == null) {
            $array = array("Validacion" => true);
            $response = new JsonResponse($array);
            return $response;
        }
    }

    /**
     * @Route("/cambiarFecha/", options = { "expose" = true }, name="cambiarFecha")
     */
    public function CambiarFecha(Request $request)
    {

            if (!$request->isXmlHttpRequest()) {
                throw new Exception("Ups! This is not an Ajax call");
            }
            $url = $request->request->get('RutaPrestamo');
            $fechaString = $request->request->get('fecha');
            $fechaDate = new \DateTime($fechaString);
            $fechaCuotas = new \DateTime($fechaString);
            $em = $this->getDoctrine()->getManager();
            $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $url));
            $prestamo->setFechaPrestamo($fechaDate);
            $cuotas = $prestamo->getPagoCuota();
            foreach($cuotas as $cuota){
                switch($prestamo->getModoPago()){
                    case 'Diario':
                        $fechaCuotas->modify('+1 day');
                        break;
                    case 'Semanal':
                        $fechaCuotas->modify('+7 day');
                        break;
                    case 'Quincenal':
                        $fechaCuotas->modify('+15 day');
                        break;
                    case 'Mensual':
                        $fechaCuotas->modify('+31 day');
                        break;
                }
                $cuota->setFechaPago($fechaCuotas);
                $em->flush();
            }
            $respuestaJson = array("Validacion" => 'true');
            $response = new JsonResponse($respuestaJson);
            return $response;
    }


    /**
     * @Route("/cambiarFechaCuota/", options = { "expose" = true }, name="cambiarFechaCuota")
     */
    public function CambiarFechaCuota(Request $request){
        if (!$request->isXmlHttpRequest()) {
            throw new Exception("Ups! This is not an Ajax call");
        }
        $id = $request->request->get('id');
        $fechaString = $request->request->get('fecha');
        $fechaDate = new \DateTime($fechaString);
        $em = $this->getDoctrine()->getManager();
        $cuota = $em->getRepository(PagoCuota::class)->find($id);
        $cuota->setFechaPago($fechaDate);
        $em->flush();
        $respuestaJson = array("Validacion" => "Cambio de fecha realizado exitosamente");
        $response = new JsonResponse($respuestaJson);
        return $response;

    }


    /**
     * @Route("realizar_abono", options = { "expose" = true }, name="realizar_abono")
     */
    public function realizar_abono(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new Exception("Ups! This is not an Ajax call");
        }
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $data['url']));
        $cuotas = $em->getRepository(PagoCuota::class)->findBy(array('prestamo' => $prestamo));
        if ($prestamo) {
            $pagado = $prestamo->getPagado();
            $abono = $data['abono'];
            $total = $pagado + $abono;
            $prestamo->setPagado($total);

            $RegistroPago = new RegistroPago(new \DateTime(), $prestamo->getCobrador()->getCedula(), 6000, $abono);
            $RegistroPago->setAccion("Recibio pago cuota");
            $em->persist($RegistroPago);

            foreach ($cuotas as $cuota) {
                if (($abono >= $cuota->getSaldo()) && (($cuota->getEstado() == 'Activo') || ($cuota->getEstado() == 'En mora'))) {
                    $cuota->setEstado('Saldado');
                    $cuota->setFechaCancelacion(new \DateTime());
                    $abono -= $cuota->getSaldo();

                } elseif (($abono < $cuota->getSaldo()) && (($cuota->getEstado() == 'Activo') || ($cuota->getEstado() == 'En mora'))) {
                    $saldo = $cuota->getSaldo();
                    $cuota->setSaldo($saldo - $abono);
                    $cuota->setFechaCancelacion(new \DateTime());

                    $abono = 0;
                }

                $em->flush();
            }


            //$array= array('Validacion'=>$abono);
            $response = new JsonResponse($cuotas);
            return $response;
        }
    }


    /**
     * @Route("crear_prestamo", name="crear_prestamo")
     */
    public function crear_prestamo(Request $request)
    {

        return $this->render('prestamos/prestamos_create.html.twig');
    }



    /**
     *  @Route("crear_informe_cuota/{id_cuota}/{prestamo_id}/{numero}", name="crear_informe_cuota")
     */
    function crear_informe($id_cuota, $prestamo_id, $numero){
        $em = $this->getDoctrine()->getManager();
        $cuota = $em->getRepository(PagoCuota::class)->find($id_cuota);
        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $prestamo_id));
        $currentDate = new \DateTime();

        $PHPWord = new PHPWord();

        $section = $PHPWord->addSection();

        $fontStyleName = 'oneUserDefinedStyle';
        $PHPWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true,'align'=>'both')
        );
        $PHPWord->addParagraphStyle('p2Style', array('align'=>'center', 'spaceAfter'=>100));


        $text = "Inversiones Fenix";
        $PHPWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
        $PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
        $section->addText($text, 'r2Style', 'p2Style');
        $section->addText('Recibo de pago');
        $PHPWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
        $PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
        $section->addText(
            'Recibo generado el día '
            .$currentDate->format('Y-m-d') . ' como constancia de pago por parte del cliente '
            . $prestamo->getCliente()->getNombre() . ' identificado con el número de cedula: ' . $prestamo->getCliente()->getCedula()
            . ' de la cuota #' . ($numero + 1) . ' Cuota id: COT-' . $cuota->getId()
        );
        $section->addTextBreak(1);
        $section->addText('Valor del préstamo: '.$prestamo->getValorPrestamo(),$fontStyleName);
        $section->addText('Valor de esta cuota: '.$cuota->getSaldo(),$fontStyleName);
        $section->addText('Estado de esta cuota: '.$cuota->getEstado(),$fontStyleName );
        $section->addText('Cobrado por '.$prestamo->getCobrador()->getNombre());
//
//        $section->addTextBreak(2);
//
//        $section->addText('A continuación, se describen los detalles del préstamo');
//        $table = $section->addTable();
//        $table->addRow();
//        $table->addCell(4000)->addText("Cobrador");
//        $table->addCell(3000)->addText("Total Pagado:");
//        $table->addCell(2000)->addText("Fecha de realización:");
//        $table->addRow();
//        $table->addCell(4000)->addText($prestamo->getCobrador()->getNombre());
//        $table->addCell(3000)->addText($prestamo->getPagado());
//        $table->addCell(2000)->addText($prestamo->getFechaPrestamo()->format('Y-m-d'));
//        $section->addText('Valor del préstamo: '.$prestamo->getValorPrestamo(),$fontStyleName);
//        $section->addText('Valor de esta cuota: '.$cuota->getSaldo(),$fontStyleName);
//        $section->addText('Estado de esta cuota: '.$cuota->getEstado(),$fontStyleName );
//        $section->addTextBreak(2);
//
//
//
//
//        $section->addTextBreak(6);
//
//
//        $tableTwo = $section->addTable();
//        $tableTwo->addRow();
//        $tableTwo->addCell(6000)->addText("Cobrador");
//        $tableTwo->addCell(6000)->addText("Cliente");
//
//
//        $tableTwo->addRow();
//        $tableTwo->addCell(6000)->addText("");
//        $tableTwo->addCell(6000)->addText("");
//
//
//        $tableTwo->addRow();
//        $tableTwo->addCell(6000)->addText("");
//        $tableTwo->addCell(6000)->addText("");
//        $tableTwo->addRow();
//        $tableTwo->addCell(6000)->addText("____________________________________");
//        $tableTwo->addCell(6000)->addText("____________________________________");
//
//
//        $section->addTextBreak(4);
//        $text2 = "© 2018 Proyecto Fenix. Republica Dominicana.";
//        $PHPWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
//        $PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
//        $section->addText($text2, 'r2Style', 'p2Style');
//
//        $text3 = "Todos los derechos reservados.";
//        $PHPWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
//        $PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
//        $section->addText($text3, 'r2Style', 'p2Style');
        $idcuota = $cuota->getId();
        $Cedula =$prestamo->getCliente()->getCedula();
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($PHPWord, 'Word2007');
        $filename="COT-$idcuota $Cedula.docx";
        $objWriter->save($filename);
        //$path = $filename;
        $content = file_get_contents($filename);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);
        $response->setContent($content);
        return $response;
    }


//    /**
//     *
//     * @param $length
//     * @param string $keyspace
//     * @return string
//     * @Route("crear_informe_cuota/{id_cuota}/{prestamo_id}/{numero}", name="crear_informe_cuota")
//     */
//    function crear_informe($id_cuota, $prestamo_id, $numero)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $cuota = $em->getRepository(PagoCuota::class)->find($id_cuota);
//        $prestamo = $em->getRepository(Prestamo::class)->findOneBy(array('url' => $prestamo_id));
//        $currentDate = new \DateTime();
//        $pdf = new \FPDF();
//        $pdf->AddPage();
//        $pdf->SetFont('Arial', '', 16);
//        $pdf->SetXY(50, 15);
//        $pdf->Cell(100, 10, 'Recibo de pago', 0, 1, 'C');
//        $pdf->MultiCell(180, 10,
//            utf8_decode('Recibo generado el dia '
//                . $currentDate->format('Y-m-d') . ' como constancia de pago por parte del cliente '
//                . $prestamo->getCliente()->getNombre() . '  identificado con el numero de cedula: ' . $prestamo->getCliente()->getCedula()
//                . ' de la cuota #' . ($numero + 1) . '  Cuota id: COT-' . $cuota->getId())
//            , 0, 'J', 0);
//
//        $pdf->Cell(150, 10, '____________________________________________________________________________________', 0, 1, 'C');
//        $pdf->Cell(150, 10, utf8_decode('A continuación se detallan los detalles del prestamo:'), 0, 1, 'C');
//        $pdf->Cell(100, 10, 'Cobrador', 1, 0, 'C');
//        $pdf->Cell(45, 10, 'Total pagado', 1, 0, 'C');
//        $pdf->Cell(40, 10, utf8_decode('F. realización'), 1, 1, 'C');
//        $pdf->Cell(100, 10, utf8_decode('' . $prestamo->getCobrador()->getNombre()), 1, 0, 'L');
//        $pdf->Cell(45, 10, '' . $prestamo->getPagado(), 1, 0, 'L');
//        $pdf->Cell(40, 10, '' . $prestamo->getFechaPrestamo()->format('Y-m-d'), 1, 1, 'L');
//        $pdf->SetFont('Arial', 'B', 16);
//        $pdf->Cell(0, 10, 'Valor del prestamo: ' . $prestamo->getValorPrestamo(), 0, 1, 'L');
//        $pdf->Cell(0, 10, 'Valor de esta cuota: ' . $cuota->getSaldo(), 0, 1, 'L');
//        $pdf->Cell(0, 10, 'Estado de esta cuota: ' . $cuota->getEstado(), 0, 1, 'L');
//        $pdf->Cell(150, 10, '____________________________________________________________________________________', 0, 1, 'C');
//        $pdf->SetFont('Arial', '', 16);
//
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//
//        $pdf->Cell(110, 10, 'Cobrador', 0, 0, 'L');
//        $pdf->Cell(100, 10, 'Cliente', 0, 1, 'L');
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//
//        $pdf->Cell(110, 10, '__________________________', 0, 0, 'L');
//        $pdf->Cell(100, 10, '__________________________', 0, 1, 'L');
//        $pdf->SetFont('Arial', '', 10);
//
//        $pdf->Cell(110, 10, 'Firma', 0, 0, 'L');
//        $pdf->Cell(100, 10, 'Firma', 0, 0, 'L');
//
//        $pdf->Cell(0, 10, '', 0, 1, 'L');
//        $pdf->SetFont('Arial', '', 10);
//
//        $pdf->Cell(0, 10, '', 0, 1, 'C');
//        $pdf->Cell(0, 10, '', 0, 1, 'C');
//
//
//        $pdf->Cell(0, 10, utf8_decode('© 2018 Proyecto Fenix. Republica Dominicana.'), 0, 1, 'C');
//
//        $pdf->Cell(0, 10, 'Todos los derechos reservados', 0, 1, 'C');
//
//
//        return new Response($pdf->Output(), 200, array(
//            'Content-Type' => 'application/pdf'));
//    }

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
