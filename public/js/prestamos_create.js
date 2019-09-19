var app = angular.module("autocomplete", ['angucomplete-alt']);
app.controller("ngAutoCompleteController", function ($scope, $http) {
    $scope.names = [];
    $scope.customerSelected = null;

    $scope.afterSelected = function (selected) {
        if (selected) {
            $scope.customerSelected = selected.originalObject;
        }
    }
    $scope.Redireccionar = function () {
        try {
            var id = $scope.customerSelected.ruta;
            var url = Routing.generate('crear_prestamo_cliente', {'ruta_cliente': id});
            window.location.href = url;
        }
        catch (err) {
            console.log("error->" + err.message);
        }

    };

    $scope.buscar = function () {
        var text = document.getElementById('ex1_value').value;

        if (text.length > 5) {
            $.ajax({
                type: 'POST',
                url: Routing.generate('search_by_name'),
                data: ({text: text}),
                dataType: "json",
                beforeSend: function () {
                    $('#buscando').html("Buscando..");

                },
                success: function (data) {
                    $scope.names = data;
                    $scope.showDropdown = true;
                    console.log($scope.names)
                    $('#buscando').html("Elementos encontrados, presione la barra espaciadora en el campo de texto para mostrar la lista de clientes. A continuación seleccione el cliente que busca. Si no lo encuentra, por favor ingrese el nombre completo o verifique su ortografía.");

                }
            })
            // $http({
            //     url: Routing.generate('search_by_name'),
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-Requested-With': 'XMLHttpRequest'
            //     },
            //     data: {text:text}
            // }).then(function successCallback(response) {
            //     if(response.data){
            //         $scope.names = response.data;
            //         console.log($scope.names);
            //         return $scope.names;
            //     }
            //
            // });
        }
    }

})


function buscarPorNombre() {
    var text = document.getElementById('ex1_value').value;
    console.log(text)

}

