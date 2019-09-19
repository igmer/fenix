var app = angular.module("miapp", []);
app.controller("clienteController", function ($scope, $http) {
    $scope.search = function () {
        var id = document.getElementById('form_id').value;
        $http({
            url: Routing.generate('ajax_search_id'),
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {id: id}
        }).then(function successCallback(response) {
            if (response['data']['Validacion'] == true) {
                var nombre = response['data']['nombre'];
                var url = response['data']['url'];
                var link = "<a href='"+url+"'>"+nombre+"</a>"
                console.log(link);
                alertify.alert("Ya existe un cliente registrado con esta identificación : " + link);
            }
        });
    }
})