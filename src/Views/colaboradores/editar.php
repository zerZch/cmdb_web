<div class="page-header">
    <h2><i class="fas fa-user-edit me-2"></i>Editar Colaborador</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=colaboradores&action=actualizar" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $colaborador['id'] ?>">

            <div class="row">
                <!-- Datos Personales -->
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="nombre"
                           name="nombre"
                           value="<?= e($colaborador['nombre']) ?>"
                           required
                           maxlength="100">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="apellido"
                           name="apellido"
                           value="<?= e($colaborador['apellido']) ?>"
                           required
                           maxlength="100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text"
                           class="form-control"
                           id="cedula"
                           name="cedula"
                           value="<?= e($colaborador['cedula'] ?? '') ?>"
                           maxlength="20">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           value="<?= e($colaborador['email'] ?? '') ?>"
                           maxlength="150">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text"
                           class="form-control"
                           id="telefono"
                           name="telefono"
                           value="<?= e($colaborador['telefono'] ?? '') ?>"
                           maxlength="20">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                    <input type="date"
                           class="form-control"
                           id="fecha_ingreso"
                           name="fecha_ingreso"
                           value="<?= e($colaborador['fecha_ingreso'] ?? '') ?>">
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-briefcase me-2"></i>Información Laboral</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text"
                           class="form-control"
                           id="cargo"
                           name="cargo"
                           value="<?= e($colaborador['cargo'] ?? '') ?>"
                           maxlength="100">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="departamento" class="form-label">Departamento</label>
                    <select class="form-select" id="departamento" name="departamento">
                        <option value="">-- Seleccionar --</option>
                        <option value="TI" <?= ($colaborador['departamento'] ?? '') === 'TI' ? 'selected' : '' ?>>TI</option>
                        <option value="Ventas" <?= ($colaborador['departamento'] ?? '') === 'Ventas' ? 'selected' : '' ?>>Ventas</option>
                        <option value="Marketing" <?= ($colaborador['departamento'] ?? '') === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                        <option value="Finanzas" <?= ($colaborador['departamento'] ?? '') === 'Finanzas' ? 'selected' : '' ?>>Finanzas</option>
                        <option value="Recursos Humanos" <?= ($colaborador['departamento'] ?? '') === 'Recursos Humanos' ? 'selected' : '' ?>>Recursos Humanos</option>
                        <option value="Administración" <?= ($colaborador['departamento'] ?? '') === 'Administración' ? 'selected' : '' ?>>Administración</option>
                        <option value="Producción" <?= ($colaborador['departamento'] ?? '') === 'Producción' ? 'selected' : '' ?>>Producción</option>
                        <option value="Otro" <?= ($colaborador['departamento'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text"
                           class="form-control"
                           id="ubicacion"
                           name="ubicacion"
                           value="<?= e($colaborador['ubicacion'] ?? '') ?>"
                           maxlength="200">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo" <?= $colaborador['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $colaborador['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        <option value="suspendido" <?= $colaborador['estado'] === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto de Perfil Actual</label>
                <div class="mb-2">
                    <?php if (!empty($colaborador['foto_perfil'])): ?>
                        <img src="<?= e($colaborador['foto_perfil']) ?>" 
                             alt="Foto actual" 
                             class="rounded"
                             style="max-width: 150px; max-height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="text-muted">Sin foto</div>
                    <?php endif; ?>
                </div>
                
                <label for="foto_perfil" class="form-label">Cambiar Foto</label>
                <input type="file"
                       class="form-control"
                       id="foto_perfil"
                       name="foto_perfil"
                       accept="image/*">
                <small class="text-muted">Dejar vacío para mantener la foto actual</small>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          rows="3"><?= e($colaborador['observaciones'] ?? '') ?></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=colaboradores" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Actualizar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>
