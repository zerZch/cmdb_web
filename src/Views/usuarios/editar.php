<div class="page-header">
    <h2><i class="fas fa-user-edit me-2"></i>Editar Usuario</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=usuarios&action=actualizar">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="nombre"
                           name="nombre"
                           value="<?= e($usuario['nombre']) ?>"
                           required
                           maxlength="100">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="apellido"
                           name="apellido"
                           value="<?= e($usuario['apellido']) ?>"
                           required
                           maxlength="100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           value="<?= e($usuario['email']) ?>"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           minlength="6">
                    <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="colaborador" <?= $usuario['rol'] === 'colaborador' ? 'selected' : '' ?>>Colaborador</option>
                        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=usuarios" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
