var boton = document.getElementById('buscarclientes');
var text = document.getElementById('infoCliente');

function buscarClientes() {
    var texto = document.getElementById('infoCliente').value;
    var url = Routing.generate('ClientesDahsboardEncontrados', {'texto':texto});
    window.open(url);
}

function runScript(e) {
    if (e.keyCode == 13) {
        var texto = document.getElementById('infoCliente').value;
        var url = Routing.generate('ClientesDahsboardEncontrados', {'texto':texto});
        window.open(url);
    }
}

boton.addEventListener("click", buscarClientes);