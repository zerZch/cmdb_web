<div class="page-header">
    <h2><i class="fas fa-hand-holding-heart me-2"></i>Registrar Donación de Equipo</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=donaciones&action=guardar" enctype="multipart/form-data">
            
            <!-- Selección de Equipo -->
            <div class="mb-4">
                <label for="equipo_id" class="form-label">
                    Equipo a Donar <span class="text-danger">*</span>
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
                    <label for="fecha_donacion" class="form-label">
                        Fecha de Donación <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           class="form-control"
                           id="fecha_donacion"
                           name="fecha_donacion"
                           value="<?= date('Y-m-d') ?>"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="condicion_equipo" class="form-label">
                        Condición del Equipo <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="condicion_equipo" name="condicion_equipo" required>
                        <option value="excelente">Excelente</option>
                        <option value="bueno" selected>Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="funcional">Funcional</option>
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-building me-2"></i>Información de la Entidad Beneficiada</h5>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="entidad_beneficiada" class="form-label">
                        Nombre de la Entidad <span class="text-danger">* OBLIGATORIO</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="entidad_beneficiada"
                           name="entidad_beneficiada"
                           required
                           maxlength="200"
                           placeholder="Ej: Fundación Ayuda Social, Escuela Primaria ABC">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tipo_entidad" class="form-label">Tipo de Entidad</label>
                    <select class="form-select" id="tipo_entidad" name="tipo_entidad">
                        <option value="">-- Seleccionar --</option>
                        <option value="ong">ONG</option>
                        <option value="fundacion">Fundación</option>
                        <option value="escuela">Escuela</option>
                        <option value="universidad">Universidad</option>
                        <option value="gobierno">Institución Gubernamental</option>
                        <option value="comunidad">Comunidad</option>
                        <option value="otra">Otra</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ruc_entidad" class="form-label">RUC / Identificación</label>
                    <input type="text"
                           class="form-control"
                           id="ruc_entidad"
                           name="ruc_entidad"
                           maxlength="50">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="valor_donacion" class="form-label">Valor Estimado de Donación (USD)</label>
                    <input type="number"
                           class="form-control"
                           id="valor_donacion"
                           name="valor_donacion"
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Datos de Contacto</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="contacto_nombre" class="form-label">Nombre del Contacto</label>
                    <input type="text"
                           class="form-control"
                           id="contacto_nombre"
                           name="contacto_nombre"
                           maxlength="150">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="contacto_telefono" class="form-label">Teléfono</label>
                    <input type="text"
                           class="form-control"
                           id="contacto_telefono"
                           name="contacto_telefono"
                           maxlength="20">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="contacto_email" class="form-label">Email</label>
                    <input type="email"
                           class="form-control"
                           id="contacto_email"
                           name="contacto_email"
                           maxlength="150">
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion_entidad" class="form-label">Dirección de la Entidad</label>
                <textarea class="form-control"
                          id="direccion_entidad"
                          name="direccion_entidad"
                          rows="2"
                          placeholder="Dirección completa de la entidad beneficiada"></textarea>
            </div>

            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numero_acta" class="form-label">Número de Acta</label>
                    <input type="text"
                           class="form-control"
                           id="numero_acta"
                           name="numero_acta"
                           maxlength="100"
                           placeholder="ACTA-DON-2024-001">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="certificado_donacion" class="form-label">Certificado de Donación (PDF/Imagen)</label>
                    <input type="file"
                           class="form-control"
                           id="certificado_donacion"
                           name="certificado_donacion"
                           accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="mb-3">
                <label for="motivo_donacion" class="form-label">Motivo de la Donación</label>
                <textarea class="form-control"
                          id="motivo_donacion"
                          name="motivo_donacion"
                          rows="3"
                          placeholder="Describa el motivo o propósito de la donación"></textarea>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          rows="3"></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=donaciones" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-hand-holding-heart me-2"></i>Registrar Donación
                </button>
            </div>
        </form>
    </div>
</div>
