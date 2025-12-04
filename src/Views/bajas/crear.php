<div class="page-header">
    <h2><i class="fas fa-exclamation-triangle me-2"></i>Registrar Baja de Equipo</h2>
    <p class="text-muted">El criterio técnico es <strong>obligatorio</strong> para cumplir con la normativa.</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=bajas&action=guardar" id="formBaja">
            <!-- Selección de Equipo -->
            <div class="mb-4">
                <label for="equipo_id" class="form-label">
                    Equipo a dar de Baja <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="equipo_id" name="equipo_id" required>
                    <option value="">-- Seleccionar Equipo --</option>
                    <?php foreach ($equipos as $equipo): ?>
                        <option value="<?= $equipo['id'] ?>">
                            <?= e($equipo['nombre']) ?> 
                            (<?= e($equipo['numero_serie'] ?? 'S/N') ?>) 
                            - <?= e($equipo['categoria']) ?>
                            - Estado: <?= e($equipo['estado']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fecha_baja" class="form-label">
                        Fecha de Baja <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           class="form-control"
                           id="fecha_baja"
                           name="fecha_baja"
                           value="<?= date('Y-m-d') ?>"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="motivo_baja" class="form-label">
                        Motivo de la Baja <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="motivo_baja" name="motivo_baja" required>
                        <option value="">-- Seleccionar Motivo --</option>
                        <option value="obsolescencia">Obsolescencia Tecnológica</option>
                        <option value="daño_irreparable">Daño Irreparable</option>
                        <option value="fin_vida_util">Fin de Vida Útil</option>
                        <option value="reemplazo">Reemplazo por Equipo Nuevo</option>
                        <option value="perdida">Pérdida</option>
                        <option value="robo">Robo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>

            <!-- CRITERIO TÉCNICO - OBLIGATORIO (Requisito de Rúbrica) -->
            <div class="mb-4">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Importante:</strong> El criterio técnico es obligatorio según la normativa de gestión de activos.
                    Debe incluir una justificación técnica detallada (mínimo 20 caracteres).
                </div>

                <label for="criterio_tecnico" class="form-label">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Criterio Técnico <span class="text-danger">* OBLIGATORIO</span>
                </label>
                <textarea class="form-control"
                          id="criterio_tecnico"
                          name="criterio_tecnico"
                          rows="5"
                          required
                          minlength="20"
                          placeholder="Ejemplo: El equipo presenta fallas recurrentes en la placa madre que no pueden ser reparadas. Se han realizado 3 intentos de reparación sin éxito. El costo de reparación supera el 60% del valor actual del equipo. Además, no hay repuestos disponibles para este modelo descontinuado."></textarea>
                <div class="form-text">
                    <span id="contador">0</span> / 20 caracteres mínimo.
                    Incluya: diagnóstico técnico, intentos de reparación, costos evaluados, etc.
                </div>
            </div>

            <hr class="my-4">

            <!-- Información Adicional -->
            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="valor_residual" class="form-label">Valor Residual (USD)</label>
                    <input type="number"
                           class="form-control"
                           id="valor_residual"
                           name="valor_residual"
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="metodo_disposicion" class="form-label">Método de Disposición</label>
                    <select class="form-select" id="metodo_disposicion" name="metodo_disposicion">
                        <option value="">-- Seleccionar --</option>
                        <option value="reciclaje">Reciclaje</option>
                        <option value="destruccion">Destrucción</option>
                        <option value="venta">Venta</option>
                        <option value="donacion">Donación</option>
                        <option value="almacenamiento">Almacenamiento</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="empresa_disposicion" class="form-label">Empresa de Disposición</label>
                    <input type="text"
                           class="form-control"
                           id="empresa_disposicion"
                           name="empresa_disposicion"
                           maxlength="200"
                           placeholder="Nombre de la empresa encargada">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_acta" class="form-label">Número de Acta</label>
                    <input type="text"
                           class="form-control"
                           id="numero_acta"
                           name="numero_acta"
                           maxlength="100"
                           placeholder="ACTA-BAJA-2024-001">
                </div>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Observaciones adicionales sobre la baja"></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=bajas" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Registrar Baja
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Contador de caracteres para criterio técnico
    const criterioInput = $('#criterio_tecnico');
    const contador = $('#contador');

    criterioInput.on('input', function() {
        const length = $(this).val().length;
        contador.text(length);

        if (length < 20) {
            contador.removeClass('text-success').addClass('text-danger');
        } else {
            contador.removeClass('text-danger').addClass('text-success');
        }
    });

    // Validación del formulario
    $('#formBaja').on('submit', function(e) {
        const criterioLength = criterioInput.val().length;

        if (criterioLength < 20) {
            e.preventDefault();
            Swal.fire({
                title: 'Criterio Técnico Insuficiente',
                text: 'El criterio técnico debe tener al menos 20 caracteres. Por favor, proporcione una justificación técnica más detallada.',
                icon: 'warning',
                confirmButtonColor: '#1B3C53'
            });
            criterioInput.focus();
            return false;
        }
    });
});
</script>
