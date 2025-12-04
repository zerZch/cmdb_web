<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?route=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?route=equipos">Equipos</a></li>
        <li class="breadcrumb-item active">Nuevo Equipo</li>
    </ol>
</nav>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus"></i>
            Registrar Nuevo Equipo
        </h5>
    </div>
    
    <form id="equipoForm" action="index.php?route=equipos&action=store" method="POST" enctype="multipart/form-data">
        
        <div class="card-body">
            
            <!-- Información Básica -->
            <div class="row">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle text-primary"></i> Información Básica
                    </h6>
                </div>
            </div>

            <div class="row">
                <!-- Categoría -->
                <div class="col-md-6 mb-3">
                    <label for="categoria_id" class="form-label">
                        Categoría <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="categoria_id" name="categoria_id" required>
                        <option value="">-- Seleccionar categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= e($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Estado -->
                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">
                        Estado <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="disponible">Disponible</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="dañado">Dañado</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Marca -->
                <div class="col-md-4 mb-3">
                    <label for="marca" class="form-label">
                        Marca <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="marca" 
                           name="marca" 
                           placeholder="Ej: Dell, HP, Lenovo"
                           required>
                </div>

                <!-- Modelo -->
                <div class="col-md-4 mb-3">
                    <label for="modelo" class="form-label">
                        Modelo <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="modelo" 
                           name="modelo" 
                           placeholder="Ej: XPS 15, ProBook 450"
                           required>
                </div>

                <!-- Número de Serie -->
                <div class="col-md-4 mb-3">
                    <label for="numero_serie" class="form-label">
                        Número de Serie <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="numero_serie" 
                           name="numero_serie" 
                           placeholder="Número único del fabricante"
                           required>
                    <small class="form-text text-muted">Debe ser único</small>
                </div>
            </div>

            <!-- Información Financiera -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-dollar-sign text-success"></i> Información Financiera
                    </h6>
                </div>
            </div>

            <div class="row">
                <!-- Fecha de Adquisición -->
                <div class="col-md-3 mb-3">
                    <label for="fecha_adquisicion" class="form-label">
                        Fecha Adquisición <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control" 
                           id="fecha_adquisicion" 
                           name="fecha_adquisicion" 
                           max="<?= date('Y-m-d') ?>"
                           required>
                </div>

                <!-- Costo de Adquisición -->
                <div class="col-md-3 mb-3">
                    <label for="costo_adquisicion" class="form-label">
                        Costo Adquisición <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               class="form-control" 
                               id="costo_adquisicion" 
                               name="costo_adquisicion" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               required>
                    </div>
                </div>

                <!-- Vida Útil -->
                <div class="col-md-3 mb-3">
                    <label for="vida_util_anos" class="form-label">
                        Vida Útil (años)
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="vida_util_anos" 
                           name="vida_util_anos" 
                           value="5"
                           min="1"
                           max="50"
                           placeholder="5">
                    <small class="form-text text-muted">Por defecto: 5 años</small>
                </div>

                <!-- Valor Residual -->
                <div class="col-md-3 mb-3">
                    <label for="valor_residual" class="form-label">
                        Valor Residual
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               class="form-control" 
                               id="valor_residual" 
                               name="valor_residual" 
                               value="0"
                               step="0.01"
                               min="0"
                               placeholder="0.00">
                    </div>
                    <small class="form-text text-muted">Valor estimado al final de vida útil</small>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-clipboard text-info"></i> Información Adicional
                    </h6>
                </div>
            </div>

            <div class="row">
                <!-- Ubicación -->
                <div class="col-md-6 mb-3">
                    <label for="ubicacion" class="form-label">
                        Ubicación Física
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="ubicacion" 
                           name="ubicacion" 
                           placeholder="Ej: Oficina 203, Almacén TI, Piso 2">
                </div>

                <!-- Foto del Equipo -->
                <div class="col-md-6 mb-3">
                    <label for="foto" class="form-label">
                        Foto del Equipo
                    </label>
                    <input type="file" 
                           class="form-control" 
                           id="foto" 
                           name="foto" 
                           accept="image/jpeg,image/png,image/jpg">
                    <small class="form-text text-muted">JPG, PNG. Máx 2MB</small>
                </div>
            </div>

            <div class="row">
                <!-- Descripción -->
                <div class="col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        Descripción / Características
                    </label>
                    <textarea class="form-control" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3"
                              placeholder="Características técnicas, configuración, observaciones..."></textarea>
                </div>
            </div>

        </div>

        <!-- Footer con botones -->
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Los campos marcados con <span class="text-danger">*</span> son obligatorios
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="index.php?route=equipos" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Guardar Equipo
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#equipoForm').on('submit', function(e) {
        const numeroSerie = $('#numero_serie').val().trim();
        const costo = parseFloat($('#costo_adquisicion').val());
        const valorResidual = parseFloat($('#valor_residual').val());
        
        if (numeroSerie.length < 3) {
            e.preventDefault();
            Swal.fire('Error', 'El número de serie debe tener al menos 3 caracteres', 'error');
            return false;
        }
        
        if (valorResidual >= costo) {
            e.preventDefault();
            Swal.fire('Error', 'El valor residual no puede ser mayor o igual al costo de adquisición', 'error');
            return false;
        }
        
        // Mostrar loading
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    });
    
    // Validar tamaño de archivo
    $('#foto').on('change', function() {
        const file = this.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'La imagen no puede superar los 2MB', 'error');
            $(this).val('');
        }
    });
});
</script>