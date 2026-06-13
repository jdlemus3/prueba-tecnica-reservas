<?php $__env->startSection('contenido'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">⚠️ No se pudo crear la reserva:</h5>
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('exito')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('exito')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 card-title">📅 Crear Nueva Reserva</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(url('/reservar')); ?>" method="POST">
                    <?php echo csrf_field(); ?> <div class="mb-3">
                        <label for="usuario_id" class="form-label font-weight-bold">👤 Seleccione el Usuario:</label>
                        <select class="form-select" id="usuario_id" name="usuario_id" required>
                            <option value="" selected disabled>-- Seleccione un usuario --</option>
                            <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($usuario->id); ?>"><?php echo e($usuario->nombre); ?> (Plan: <?php echo e(ucfirst($usuario->tipo_plan)); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="profesional_id" class="form-label">🧑‍⚕️ Seleccione el Profesional:</label>
                        <select class="form-select" id="profesional_id" name="profesional_id" required>
                            <option value="" selected disabled>-- Seleccione un profesional --</option>
                            <?php $__currentLoopData = $profesionales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profesional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($profesional->id); ?>"><?php echo e($profesional->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="servicio_id" class="form-label">💼 Seleccione el Servicio:</label>
                        <select class="form-select" id="servicio_id" name="servicio_id" required>
                            <option value="" selected disabled>-- Seleccione un servicio --</option>
                            <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servicio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($servicio->id); ?>"><?php echo e($servicio->nombre); ?> - $<?php echo e(number_format($servicio->precio, 0)); ?> (<?php echo e($servicio->duracion_minutos); ?> min)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="fecha_inicio" class="form-label">⏰ Fecha y Hora de la Cita:</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        <div class="form-text text-muted">Horario: Lunes a Sábado de 7:00 a 19:00 (No festivos).</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">💾 Registrar Reserva</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 card-title">📋 Listado de Reservas Registradas</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Profesional</th>
                                <th>Servicio</th>
                                <th>Fecha Inicio</th>
                                <th>Estado</th>
                                <th>Monto Reembolsado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $reservas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reserva): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong>#<?php echo e($reserva->id); ?></strong></td>
                                    <td><?php echo e($reserva->usuario->nombre); ?></td>
                                    <td><?php echo e($reserva->profesional->nombre); ?></td>
                                    <td><?php echo e($reserva->servicio->nombre); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i')); ?></td>
                                    <td>
                                        <?php if($reserva->estado == 'activa'): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($reserva->monto_reembolsado > 0): ?>
                                            <span class="text-success font-weight-bold">$<?php echo e(number_format($reserva->monto_reembolsado, 0)); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">$0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($reserva->estado == 'activa'): ?>
                                            <form action="<?php echo e(url('/cancelar/'.$reserva->id)); ?>" method="POST" onsubmit="return confirm('¿Está seguro de cancelar esta reserva?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm">❌ Cancelar</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Ninguna</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No hay ninguna reserva registrada en el sistema todavía.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/prueba/public_html/resources/views/reservas/crear.blade.php ENDPATH**/ ?>