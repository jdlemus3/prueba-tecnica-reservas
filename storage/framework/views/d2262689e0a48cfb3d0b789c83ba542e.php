<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas - Prueba Técnica</title>
    <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(url('/')); ?>">Gestión de Reservas IA</a>
        </div>
    </nav>

    <div class="container">
        <?php echo $__env->yieldContent('contenido'); ?>
    </div>

    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
</body>
</html><?php /**PATH /home/prueba/public_html/resources/views/layouts/app.blade.php ENDPATH**/ ?>