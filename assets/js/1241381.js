document.addEventListener('DOMContentLoaded', function () {
    const formularioContacto = document.getElementById('form-contacto');
    const alertaSucesso = document.getElementById('alerta-sucesso');

    if (!formularioContacto || !alertaSucesso) {
        return;
    }

    formularioContacto.addEventListener('submit', function (evento) {
        evento.preventDefault();
        alertaSucesso.classList.remove('d-none');
        formularioContacto.reset();

        window.setTimeout(function () {
            alertaSucesso.classList.add('d-none');
        }, 5000);
    });
});
