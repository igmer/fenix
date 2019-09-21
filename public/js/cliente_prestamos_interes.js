var app = angular.module("cliente_prestamos_app", []);
app.controller("cliente_prestamos_Controller", function ($scope, $window) {
    $scope.prestamo=0;
    $scope.total = 0;
    $scope.tasa_interes= 20;
    $scope.modo_pago="Diario";
    $scope.dias_pago=0;
    $scope.total_cuotas=0;
    $scope.pago_cuota=0;
    $scope.diario= false;
    $scope.semanal=true;
    $scope.quincenal=true;
    $scope.mensual = true;
    $scope.semanasPago='1 semana';
    $scope.QuincenasPago='1 Quincena';
    $scope.MesesPago = '1 Mes';


    $scope.procesar = function () {

        $scope.total=(((($scope.prestamo*$scope.tasa_interes)/100)*$scope.dias_pago)+$scope.prestamo);
        var conversion = 0;
        switch ($scope.modo_pago){
            case "Diario":
                $scope.diario=false;
                $scope.semanal=true;
                $scope.quincenal=true;
                $scope.mensual=true;

                $scope.total_cuotas=$scope.dias_pago/1;
                break;
            case "Semanal":
                $scope.semanal=false;
                $scope.diario=true;
                $scope.quincenal=true;
                $scope.mensual=true;


                conversion= $scope.semanasPago.split(" ")[0];
                $scope.dias_pago=(parseInt(conversion)*7);
                $scope.total_cuotas=$window.Math.round($scope.dias_pago/7);
                break;
            case "Quincenal":
                $scope.quincenal=false;
                $scope.semanal=true;
                $scope.mensual=true;
                $scope.diario=true;
                conversion= $scope.QuincenasPago.split(" ")[0];
                $scope.dias_pago=(parseInt(conversion)*15);
                $scope.total_cuotas=$window.Math.round($scope.dias_pago/15);
                break;
            case "Mensual":
                $scope.mensual=false;
                $scope.quincenal=true;
                $scope.semanal=true;
                $scope.diario=true;
                conversion = $scope.MesesPago.split(" ")[0];
                $scope.dias_pago=(parseInt(conversion)*30);
                $scope.total_cuotas=$window.Math.round($scope.dias_pago/30);
                break;
        }

        if($scope.total_cuotas==0 && $scope.total!=0){
            $scope.total_cuotas=1;
        }
        $scope.pago_cuota = (($scope.prestamo*$scope.tasa_interes)/100);
    }
})

