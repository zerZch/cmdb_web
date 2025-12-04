<!-- Vista: src/Views/equipos/importar.php -->
<div class="row">
    <div class="col-lg-10 mx-auto">
        
        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-upload me-2"></i>Importación Masiva de Equipos</h2>
            <a href="index.php?route=equipos" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>

        <!-- Instrucciones -->
        <div class="card mb-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Instrucciones de Importación</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">
                        <strong>Descarga la plantilla CSV</strong> haciendo clic en el botón de abajo
                    </li>
                    <li class="mb-2">
                        <strong>Llena la plantilla</strong> con los datos de tus equipos siguiendo el formato de los ejemplos
                    </li>
                    <li class="mb-2">
                        <strong>Guarda el archivo</strong> asegurándote de mantener el formato CSV
                    </li>
                    <li class="mb-2">
                        <strong>Sube el archivo</strong> completado usando el formulario de abajo
                    </li>
                    <li class="mb-2">
                        El sistema validará los datos y creará los equipos automáticamente
                    </li>
                    <li>
                        <i class="fas fa-qrcode me-1"></i>
                        <strong>Nota:</strong> Los códigos QR se generan manualmente desde la ficha de cada equipo después de la importación
                    </li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- Paso 1: Descargar Plantilla -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-download me-2"></i>Paso 1: Descargar Plantilla
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-file-csv fa-5x text-primary mb-3"></i>
                        <p class="text-muted mb-4">
                            Descarga la plantilla oficial con el formato correcto y ejemplos incluidos
                        </p>
                        <a href="index.php?route=equipos&action=descargarPlantilla" 
                           class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i>Descargar Plantilla CSV
                        </a>
                        <div class="alert alert-info mt-3 text-start">
                            <small>
                                <i class="fas fa-lightbulb me-1"></i>
                                <strong>Tip:</strong> La plantilla incluye la lista de categorías disponibles al final del archivo
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Subir Archivo -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-upload me-2"></i>Paso 2: Subir Archivo CSV
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="index.php?route=equipos&action=procesarImportacion" 
                              method="POST" 
                              enctype="multipart/form-data"
                              id="formImportar">
                            
                            <div class="mb-4 text-center">
                                <i class="fas fa-cloud-upload-alt fa-5x text-success mb-3"></i>
                                <p class="text-muted">
                                    Selecciona el archivo CSV completado con los datos de tus equipos
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="archivo" class="form-label fw-bold">
                                    <i class="fas fa-file me-1"></i>Archivo CSV
                                </label>
                                <input type="file" 
                                       class="form-control form-control-lg" 
                                       id="archivo" 
                                       name="archivo" 
                                       accept=".csv,.txt"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Solo archivos CSV. Tamaño máximo: 10MB
                                </div>
                            </div>

                            <div id="fileInfo" class="alert alert-secondary d-none mb-3">
                                <small>
                                    <strong>Archivo seleccionado:</strong> 
                                    <span id="fileName"></span><br>
                                    <strong>Tamaño:</strong> 
                                    <span id="fileSize"></span>
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg" id="btnImportar">
                                    <i class="fas fa-upload me-2"></i>Subir e Importar
                                </button>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <small>
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Importante:</strong> La importación puede tomar varios minutos dependiendo del número de equipos.
                                    Los códigos QR deberán generarse manualmente desde cada equipo después de la importación.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categorías Disponibles -->
        <?php if (!empty($categorias)): ?>
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Categorías Disponibles
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Usa estos IDs en la columna <code>categoria_id</code> de tu archivo CSV:
                </p>
                <div class="row">
                    <?php foreach ($categorias as $categoria): ?>
                        <div class="col-md-4 mb-2">
                            <span class="badge bg-primary fs-6 w-100 text-start">
                                <strong>ID <?= $categoria['id'] ?>:</strong> 
                                <?= e($categoria['nombre']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Validaciones y Requisitos -->
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Validaciones Automáticas
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">El sistema validará automáticamente:</p>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Campos obligatorios completos
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Números de serie únicos (sin duplicados)
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Formato de fechas correcto (YYYY-MM-DD)
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Costos válidos (números positivos)
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Categorías existentes en el sistema
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Estados válidos (disponible, asignado, etc.)
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-info mb-0 mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Si hay errores, recibirás un reporte detallado con el número de fila exacto donde ocurrió cada problema
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript para manejo del archivo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('archivo');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const form = document.getElementById('formImportar');
    const btnImportar = document.getElementById('btnImportar');
    
    // Mostrar información del archivo seleccionado
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Mostrar info del archivo
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');
            
            // Validar extensión
            const extension = file.name.split('.').pop().toLowerCase();
            if (extension !== 'csv' && extension !== 'txt') {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Por favor selecciona un archivo CSV',
                    confirmButtonColor: '#dc3545'
                });
                fileInput.value = '';
                fileInfo.classList.add('d-none');
                return;
            }
            
            // Validar tamaño (10MB)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El archivo no debe superar 10MB',
                    confirmButtonColor: '#dc3545'
                });
                fileInput.value = '';
                fileInfo.classList.add('d-none');
                return;
            }
        } else {
            fileInfo.classList.add('d-none');
        }
    });
    
    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar que hay un archivo
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo requerido',
                text: 'Por favor selecciona un archivo CSV para importar',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        
        // Confirmación antes de importar
        Swal.fire({
            title: '¿Iniciar importación?',
            html: `
                <p>Se procesará el archivo: <strong>${fileInput.files[0].name}</strong></p>
                <p class="text-muted">Esta operación puede tomar varios minutos.</p>
                <p class="text-warning"><i class="fas fa-info-circle"></i> Los códigos QR deberán generarse manualmente después.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-upload me-2"></i>Sí, importar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: () => {
                // Deshabilitar botón
                btnImportar.disabled = true;
                btnImportar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importando...';
                
                // Enviar formulario
                form.submit();
            }
        });
    });
    
    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>

<style>
/* Estilos adicionales para la vista de importación */
.card-header h5 {
    font-weight: 600;
}

.fa-5x {
    font-size: 4rem;
}

#fileInfo {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control-lg {
    padding: 0.75rem 1rem;
}

.badge.fs-6 {
    padding: 0.5rem 0.75rem;
}

/* Hover effects */
.btn-primary:hover,
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .fa-5x {
        font-size: 3rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
}
</style>