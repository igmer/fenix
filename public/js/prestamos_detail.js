var app = angular.module("prestamodetail_app", []);
app.controller("prestamodetailController", function ($scope, $http) {
    $scope.pagarcuota = function (url, id) {
        $http({
            url: Routing.generate('pagar_cuota'),
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {id: id, url: url}
        }).then(function successCallback(response) {
            if (response['data']['Validacion'] == true) {
                window.location.reload();
            }

        })
    }

    $scope.abonar = function (url, pagado, total) {
        var abono = parseInt(document.getElementById('abono').value);
        if (isNaN(abono)) {
            alert("No ha ingresado un valor valido para realizar el abono");
        } else {
            if ((abono + pagado) > total) {
                alert("El dinero que será abonado sobrepasa el total del prestamo acordado")
            }
            else {
                $http({
                    url: Routing.generate('realizar_abono'),
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {abono: abono, url: url}
                }).then(function successCallback(response) {
                    console.log(response.data)
                    location.reload();
                })
            }
        }
    }


    $scope.grafica = function (abonado, total) {
        var gg1 = new JustGage({
            id: "gg1",
            value: abonado,
            min: 0,
            max: total,
            pointer: true,
            pointerOptions: {
                toplength: -15,
                bottomlength: 10,
                bottomwidth: 12,
                color: '#8e8e93',
                stroke: '#ffffff',
                stroke_width: 3,
                stroke_linecap: 'round'
            },
            gaugeWidthScale: 0.6,
            counter: true,
            formatNumber: true
        });
    }


})
