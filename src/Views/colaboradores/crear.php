<div class="page-header">
    <h2><i class="fas fa-user-plus me-2"></i>Crear Nuevo Colaborador</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=colaboradores&action=guardar" enctype="multipart/form-data">
            <div class="row">
                <!-- Datos Personales -->
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="nombre"
                           name="nombre"
                           required
                           maxlength="100">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="apellido"
                           name="apellido"
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
                           maxlength="20"
                           placeholder="8-123-4567">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
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
                           maxlength="20"
                           placeholder="6789-0123">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                    <input type="date"
                           class="form-control"
                           id="fecha_ingreso"
                           name="fecha_ingreso">
                </div>
            </div>

            <!-- Datos Laborales -->
            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-briefcase me-2"></i>Información Laboral</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text"
                           class="form-control"
                           id="cargo"
                           name="cargo"
                           maxlength="100"
                           placeholder="Ej: Desarrollador, Contador, etc.">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="departamento" class="form-label">Departamento</label>
                    <select class="form-select" id="departamento" name="departamento">
                        <option value="">-- Seleccionar --</option>
                        <option value="TI">TI</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Finanzas">Finanzas</option>
                        <option value="Recursos Humanos">Recursos Humanos</option>
                        <option value="Administración">Administración</option>
                        <option value="Producción">Producción</option>
                        <option value="Otro">Otro</option>
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
                           maxlength="200"
                           placeholder="Ej: Oficina 201, Piso 3">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>
            </div>

            <!-- Foto de Perfil -->
            <div class="mb-3">
                <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                <input type="file"
                       class="form-control"
                       id="foto_perfil"
                       name="foto_perfil"
                       accept="image/*">
                <small class="text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</small>
            </div>

            <!-- Observaciones -->
            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales sobre el colaborador"></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=colaboradores" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Guardar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview de la foto antes de subir
document.getElementById('foto_perfil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'La imagen es muy grande. Máximo 2MB.', 'error');
            this.value = '';
        }
    }
});
</script>
