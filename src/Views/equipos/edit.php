<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?route=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?route=equipos">Equipos</a></li>
        <li class="breadcrumb-item active">Editar Equipo</li>
    </ol>
</nav>

<div class="card shadow">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-edit"></i>
            Editar Equipo: <?= e($equipo['codigo_inventario']) ?>
        </h5>
    </div>
    
    <form id="equipoForm" action="index.php?route=equipos&action=update&id=<?= $equipo['id'] ?>" method="POST" enctype="multipart/form-data">
        
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
                            <option value="<?= $cat['id'] ?>" <?= $equipo['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
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
                        <option value="disponible" <?= $equipo['estado'] == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                        <option value="asignado" <?= $equipo['estado'] == 'asignado' ? 'selected' : '' ?>>Asignado</option>
                        <option value="mantenimiento" <?= $equipo['estado'] == 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                        <option value="dañado" <?= $equipo['estado'] == 'dañado' ? 'selected' : '' ?>>Dañado</option>
                        <option value="dado_de_baja" <?= $equipo['estado'] == 'dado_de_baja' ? 'selected' : '' ?>>Baja</option>
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
                           value="<?= e($equipo['marca']) ?>"
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
                           value="<?= e($equipo['modelo']) ?>"
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
                           value="<?= e($equipo['numero_serie']) ?>"
                           placeholder="Número único del fabricante"
                           data-original="<?= e($equipo['numero_serie']) ?>"
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
                           value="<?= $equipo['fecha_adquisicion'] ?>"
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
                               value="<?= $equipo['costo_adquisicion'] ?>"
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
                           value="<?= $equipo['vida_util_anos'] ?? 5 ?>"
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
                               value="<?= $equipo['valor_residual'] ?? 0 ?>"
                               step="0.01"
                               min="0"
                               placeholder="0.00">
                    </div>
                    <small class="form-text text-muted">Valor estimado al final de vida útil</small>
                </div>
            </div>

            <!-- Vista Previa de Depreciación -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-chart-line"></i> Información de Depreciación (Vista Previa)
                        </h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <small class="d-block text-muted">Depreciación Mensual</small>
                                <strong id="preview-dep-mensual">$0.00</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="d-block text-muted">Dep. Acumulada</small>
                                <strong id="preview-dep-acumulada">$0.00</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="d-block text-muted">Valor en Libros</small>
                                <strong id="preview-valor-libro">$0.00</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="d-block text-muted">% Depreciado</small>
                                <strong id="preview-porcentaje">0%</strong>
                            </div>
                        </div>
                    </div>
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
                           value="<?= e($equipo['ubicacion'] ?? '') ?>"
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
                    <small class="form-text text-muted">JPG, PNG. Máx 2MB (dejar vacío para mantener la actual)</small>
                    
                    <?php if (!empty($equipo['foto'])): ?>
                        <div class="mt-2">
                            <img src="<?= $equipo['foto'] ?>" 
                                 alt="Foto actual" 
                                 class="img-thumbnail" 
                                 style="max-width: 150px;">
                            <p class="small text-muted mb-0">Foto actual</p>
                        </div>
                    <?php endif; ?>
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
                              placeholder="Características técnicas, configuración, observaciones..."><?= e($equipo['descripcion'] ?? '') ?></textarea>
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
                    <button type="submit" class="btn btn-warning text-white" id="btnSubmit">
                        <i class="fas fa-save"></i> Actualizar Equipo
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
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
    });
    
    // Calcular depreciación en tiempo real
    function calcularDepreciacion() {
        const costo = parseFloat($('#costo_adquisicion').val()) || 0;
        const valorResidual = parseFloat($('#valor_residual').val()) || 0;
        const vidaUtil = parseInt($('#vida_util_anos').val()) || 5;
        const fechaAdq = $('#fecha_adquisicion').val();
        
        if (!fechaAdq || costo === 0) return;
        
        const inicio = new Date(fechaAdq);
        const hoy = new Date();
        const mesesTranscurridos = Math.max(0, 
            (hoy.getFullYear() - inicio.getFullYear()) * 12 + 
            (hoy.getMonth() - inicio.getMonth())
        );
        
        const vidaUtilMeses = vidaUtil * 12;
        const depMensual = (costo - valorResidual) / vidaUtilMeses;
        const depAcumulada = Math.min(depMensual * mesesTranscurridos, costo - valorResidual);
        const valorLibro = Math.max(costo - depAcumulada, valorResidual);
        const porcentaje = Math.min((depAcumulada / costo) * 100, 100);
        
        $('#preview-dep-mensual').text('$' + depMensual.toFixed(2));
        $('#preview-dep-acumulada').text('$' + depAcumulada.toFixed(2));
        $('#preview-valor-libro').text('$' + valorLibro.toFixed(2));
        $('#preview-porcentaje').text(porcentaje.toFixed(2) + '%');
    }
    
    $('#costo_adquisicion, #valor_residual, #vida_util_anos, #fecha_adquisicion').on('change keyup', calcularDepreciacion);
    calcularDepreciacion();
    
    // Validar tamaño de archivo
    $('#foto').on('change', function() {
        const file = this.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'La imagen no puede superar los 2MB', 'error');
            $(this).val('');
        }
    });
    
    // Validar número de serie duplicado
    let numeroSerieOriginal = $('#numero_serie').data('original');
    $('#numero_serie').on('blur', function() {
        const nuevoNumero = $(this).val().trim();
        
        // Solo validar si cambió
        if (nuevoNumero !== numeroSerieOriginal && nuevoNumero.length >= 3) {
            $.ajax({
                url: 'index.php?route=equipos&action=verificarSerie',
                method: 'POST',
                data: { 
                    numero_serie: nuevoNumero,
                    equipo_id: <?= $equipo['id'] ?>
                },
                success: function(response) {
                    if (response.exists) {
                        Swal.fire('Error', 'Este número de serie ya existe en otro equipo', 'error');
                        $('#numero_serie').addClass('is-invalid');
                    } else {
                        $('#numero_serie').removeClass('is-invalid').addClass('is-valid');
                    }
                }
            });
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
}
.is-valid {
    border-color: #28a745 !important;
}
</style>