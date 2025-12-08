<div class="page-header">
    <h2><i class="fas fa-user-plus me-2"></i>Asignar Equipo a Colaborador</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=asignaciones&action=guardar">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="equipo_id" class="form-label">
                        Equipo <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="equipo_id" name="equipo_id" required>
                        <option value="">-- Seleccionar Equipo --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?= $equipo['id'] ?>">
                                <?= e($equipo['nombre']) ?> (<?= e($equipo['numero_serie'] ?? 'S/N') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="colaborador_id" class="form-label">
                        Colaborador <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="colaborador_id" name="colaborador_id" required>
                        <option value="">-- Seleccionar Colaborador --</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?= $colaborador['id'] ?>">
                                <?= e($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?>
                                <?php if ($colaborador['departamento']): ?>
                                    (<?= e($colaborador['departamento']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="fecha_asignacion" class="form-label">
                    Fecha de Asignación <span class="text-danger">*</span>
                </label>
                <input type="date"
                       class="form-control"
                       id="fecha_asignacion"
                       name="fecha_asignacion"
                       value="<?= date('Y-m-d') ?>"
                       required>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Observaciones adicionales sobre la asignación"></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=asignaciones" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Asignar Equipo
                </button>
            </div>
        </form>
    </div>
</div>