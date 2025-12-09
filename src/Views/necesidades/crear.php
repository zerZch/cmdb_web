<div class="page-header">
    <h2><i class="fas fa-plus-circle me-2"></i>Nueva Solicitud de Equipo</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=necesidades&action=guardar">

            <!-- Categoría -->
            <div class="mb-3">
                <label for="categoria_id" class="form-label">
                    Categoría <span class="text-danger">*</span>
                </label>
                <select name="categoria_id" id="categoria_id" class="form-select" required>
                    <option value="">-- Seleccionar categoría --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>">
                            <?= e($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo de equipo -->
            <div class="mb-3">
                <label for="tipo_equipo" class="form-label">
                    Tipo de equipo (opcional)
                </label>
                <input 
                    type="text" 
                    name="tipo_equipo" 
                    id="tipo_equipo" 
                    class="form-control"
                    placeholder="Ej: Laptop, Mouse, Teclado">
            </div>

            <!-- Urgencia -->
            <div class="mb-3">
                <label class="form-label">Urgencia</label>
                <select name="urgencia" class="form-select">
                    <option value="baja">Baja</option>
                    <option value="normal" selected>Normal</option>
                    <option value="alta">Alta</option>
                </select>
            </div>

            <!-- Justificación -->
            <div class="mb-3">
                <label for="justificacion" class="form-label">
                    Justificación <span class="text-danger">*</span>
                </label>
                <textarea 
                    name="justificacion" 
                    id="justificacion" 
                    rows="4" 
                    class="form-control"
                    required
                    placeholder="Explica por qué necesitas este equipo"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=necesidades&action=misSolicitudes" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Mis solicitudes
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Enviar solicitud
                </button>
            </div>
        </form>
    </div>
</div>
