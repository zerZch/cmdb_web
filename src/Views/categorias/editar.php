<div class="page-header">
    <h2><i class="fas fa-tag me-2"></i>Editar Categoría</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=categorias&action=actualizar">
            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control"
                       id="nombre"
                       name="nombre"
                       value="<?= e($categoria['nombre']) ?>"
                       required
                       maxlength="100">
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control"
                          id="descripcion"
                          name="descripcion"
                          rows="3"><?= e($categoria['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="activa" <?= $categoria['estado'] === 'activa' ? 'selected' : '' ?>>Activa</option>
                    <option value="inactiva" <?= $categoria['estado'] === 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=categorias" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Actualizar Categoría
                </button>
            </div>
        </form>
    </div>
</div>
