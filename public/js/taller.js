$(document).ready(function() {
    // Función para cargar talleres disponibles
    function cargarTalleres() {
        $.ajax({
            url: 'index.php?option=talleres_json',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let html = '<div class="row">';
                if (data.length === 0) {
                    html = '<div class="alert alert-warning">No hay talleres disponibles</div>';
                } else {
                    for (let i = 0; i < data.length; i++) {
                        let t = data[i];
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">${t.nombre}</h5>
                                        <p class="card-text">${t.descripcion || 'Sin descripción'}</p>
                                        <p class="card-text"><strong>Cupos:</strong> ${t.cupo_disponible}/${t.cupo_maximo}</p>
                                        <button class="btn btn-primary btn-solicitar" data-id="${t.id}">Solicitar inscripción</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
                html += '</div>';
                $('#talleresList').html(html);
            },
            error: function() {
                $('#talleresList').html('<div class="alert alert-danger">Error al cargar talleres</div>');
            }
        });
    }

    // Función para cargar mis solicitudes
    function cargarMisSolicitudes() {
        $.ajax({
            url: 'index.php?option=mis_solicitudes_json',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.length === 0) {
                    $('#solicitudesList').html('<div class="alert alert-info">No has realizado ninguna solicitud</div>');
                    return;
                }
                
                let html = '<table class="table table-bordered"><thead><tr><th>Taller</th><th>Fecha</th><th>Estado</th></tr></thead><tbody>';
                for (let i = 0; i < data.length; i++) {
                    let s = data[i];
                    let estado = s.estado;
                    let estadoClass = '';
                    let estadoText = '';
                    
                    if (estado === 'pendiente') {
                        estadoClass = 'bg-warning';
                        estadoText = 'Pendiente';
                    } else if (estado === 'aprobada') {
                        estadoClass = 'bg-success';
                        estadoText = 'Aprobada';
                    } else {
                        estadoClass = 'bg-danger';
                        estadoText = 'Rechazada';
                    }
                    
                    html += `<tr>
                        <td>${s.taller_nombre || 'Taller no disponible'}</td>
                        <td>${new Date(s.fecha_solicitud).toLocaleString()}</td>
                        <td><span class="badge ${estadoClass}">${estadoText}</span></td>
                    </tr>`;
                }
                html += '</tbody></table>';
                $('#solicitudesList').html(html);
            },
            error: function() {
                $('#solicitudesList').html('<div class="alert alert-danger">Error al cargar tus solicitudes</div>');
            }
        });
    }

    // Solicitar taller
    $(document).on('click', '.btn-solicitar', function() {
        let tallerId = $(this).data('id');
        let $btn = $(this);
        
        $btn.prop('disabled', true).text('Enviando...');
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: {
                option: 'solicitar',
                taller_id: tallerId
            },
            dataType: 'json',
            success: function(resp) {
                alert(resp.message);
                if (resp.success) {
                    cargarTalleres();
                    cargarMisSolicitudes();
                } else {
                    $btn.prop('disabled', false).text('Solicitar inscripción');
                }
            },
            error: function() {
                alert('Error de conexión');
                $btn.prop('disabled', false).text('Solicitar inscripción');
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

    // Cargar datos al iniciar
    cargarTalleres();
    cargarMisSolicitudes();
});