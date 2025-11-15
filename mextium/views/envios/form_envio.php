<?php
// form_envio.php
// Formulario para crear guía y rastrear envíos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Envíos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Crear Guía de Envío</h2>
    <form id="formCrearGuia" method="POST" action="/mextium/controller/guia_envio_controller.php">
        <input type="hidden" name="accion" value="crear_guia">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Remitente</label>
                <input type="text" name="remitente" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Destinatario</label>
                <input type="text" name="destinatario" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <input type="text" name="estado" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Código Postal</label>
                <input type="text" name="codigo_postal" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Valor declarado</label>
                <input type="number" step="0.01" name="valor" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Crear Guía</button>
        </div>
    </form>

    <hr class="my-5">
    <h2 class="mb-4">Rastrear Envío</h2>
    <form id="formRastrear" method="GET" action="/mextium/controller/guia_envio_controller.php">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Número de Guía</label>
                <input type="text" name="numero_guia" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-success">Rastrear</button>
        </div>
    </form>
</div>
</body>
</html>

<script>
document.getElementById('formCrearGuia').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let msg = '';
        if (data.status === 200 && data.response) {
            msg = '<div class="alert alert-success mt-4">Guía creada correctamente.<br><b>Número de guía:</b> ' + (data.response.numero_guia || 'N/A') + '</div>';
        } else {
            msg = '<div class="alert alert-danger mt-4">Error al crear la guía.<br>' + (data.response?.mensaje || 'Intenta nuevamente.') + '</div>';
        }
        mostrarResultado(msg);
    })
    .catch(() => mostrarResultado('<div class="alert alert-danger mt-4">Error de conexión.</div>'));
});

document.getElementById('formRastrear').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const params = new URLSearchParams(new FormData(form)).toString();
    fetch(form.action + '?' + params)
    .then(res => res.json())
    .then(data => {
        let msg = '';
        if (data.status === 200 && data.response) {
            msg = '<div class="alert alert-info mt-4"><b>Estado del envío:</b> ' + (data.response.estado || 'N/A') + '<br><b>Último movimiento:</b> ' + (data.response.ultimo_evento || 'N/A') + '</div>';
            if (Array.isArray(data.response.historial) && data.response.historial.length > 0) {
                msg += '<h5 class="mt-3">Historial de eventos</h5>';
                msg += '<table class="table table-bordered table-sm"><thead><tr><th>Fecha</th><th>Evento</th><th>Ubicación</th></tr></thead><tbody>';
                data.response.historial.forEach(ev => {
                    msg += '<tr>' +
                        '<td>' + (ev.fecha || '-') + '</td>' +
                        '<td>' + (ev.evento || '-') + '</td>' +
                        '<td>' + (ev.ubicacion || '-') + '</td>' +
                    '</tr>';
                });
                msg += '</tbody></table>';
            }
        } else {
            msg = '<div class="alert alert-warning mt-4">No se encontró información para ese número de guía.</div>';
        }
        mostrarResultado(msg);
    })
    .catch(() => mostrarResultado('<div class="alert alert-danger mt-4">Error de conexión.</div>'));
});

function mostrarResultado(html) {
    let div = document.getElementById('resultadoEnvio');
    if (!div) {
        div = document.createElement('div');
        div.id = 'resultadoEnvio';
        document.querySelector('.container').appendChild(div);
    }
    div.innerHTML = html;
}
</script>
