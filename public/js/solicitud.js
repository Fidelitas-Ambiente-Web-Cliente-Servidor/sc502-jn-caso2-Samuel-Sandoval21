$(document).ready(function() {
    // Cargar solicitudes pendientes
    function cargarSolicitudes() {
        $.ajax({
            url: 'index.php?option=solicitudes_json',
            type: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (!resp.success) {
                    $('#solicitudes-body').html('<tr><td colspan="6" class="text-danger">' + resp.message + '</td></tr>');
                    return;
                }
                
                let solicitudes = resp.data;
                let html = '';
                
                if (solicitudes.length === 0) {
                    html = '<tr><td colspan="6" class="text-center">No hay solicitudes pendientes</td></tr>';
                } else {
                    for (let i = 0; i < solicitudes.length; i++) {
                        let s = solicitudes[i];
                        html += `<tr>
                            <td>${s.id}</td>
                            <td>${s.taller_nombre || 'No especificado'}</td>
                            <td>${s.username || 'Desconocido'}</td>
                            <td>${s.username || 'Desconocido'}</td>
                            <td>${new Date(s.fecha_solicitud).toLocaleString()}</td>
                            <td>
                                <button class="btn btn-success btn-sm btn-aprobar" data-id="${s.id}">✅ Aprobar</button>
                                <button class="btn btn-danger btn-sm btn-rechazar" data-id="${s.id}">❌ Rechazar</button>
                            </td>
                        </tr>`;
                    }
                }
                
                $('#solicitudes-body').html(html);
            },
            error: function() {
                $('#solicitudes-body').html('<tr><td colspan="6" class="text-danger">Error de conexión</td></tr>');
            }
        });
    }

    // Aprobar solicitud
    $(document).on('click', '.btn-aprobar', function() {
        let id = $(this).data('id');
        let $btn = $(this);
        
        if (!confirm('¿Aprobar esta solicitud? Se descontará un cupo.')) return;
        
        $btn.prop('disabled', true).text('Procesando...');
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: {
                option: 'aprobar',
                id_solicitud: id
            },
            dataType: 'json',
            success: function(resp) {
                alert(resp.message);
                if (resp.success) {
                    cargarSolicitudes();
                } else {
                    $btn.prop('disabled', false).text('Aprobar');
                }
            },
            error: function() {
                alert('Error de conexión');
                $btn.prop('disabled', false).text('Aprobar');
            }
        });
    });

    // Rechazar solicitud
    $(document).on('click', '.btn-rechazar', function() {
        let id = $(this).data('id');
        let $btn = $(this);
        
        if (!confirm('¿Rechazar esta solicitud?')) return;
        
        $btn.prop('disabled', true).text('Procesando...');
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: {
                option: 'rechazar',
                id_solicitud: id
            },
            dataType: 'json',
            success: function(resp) {
                alert(resp.message);
                if (resp.success) {
                    cargarSolicitudes();
                } else {
                    $btn.prop('disabled', false).text('Rechazar');
                }
            },
            error: function() {
                alert('Error de conexión');
                $btn.prop('disabled', false).text('Rechazar');
            }
        });
    });

    // Logout
    $('#btnLogout').on('click', function() {
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: { option: 'logout' },
            dataType: 'json',
            success: function() {
                window.location.href = 'index.php?page=login';
            }
        });
    });

    // Cargar solicitudes al iniciar
    cargarSolicitudes();
});