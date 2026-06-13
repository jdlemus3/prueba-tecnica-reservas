@extends('layouts.app')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">⚠️ No se pudo crear la reserva:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('exito'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('exito') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 card-title">📅 Crear Nueva Reserva</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('/reservar') }}" method="POST">
                    @csrf <div class="mb-3">
                        <label for="usuario_id" class="form-label font-weight-bold">👤 Seleccione el Usuario:</label>
                        <select class="form-select" id="usuario_id" name="usuario_id" required>
                            <option value="" selected disabled>-- Seleccione un usuario --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->nombre }} (Plan: {{ ucfirst($usuario->tipo_plan) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="profesional_id" class="form-label">🧑‍⚕️ Seleccione el Profesional:</label>
                        <select class="form-select" id="profesional_id" name="profesional_id" required>
                            <option value="" selected disabled>-- Seleccione un profesional --</option>
                            @foreach($profesionales as $profesional)
                                <option value="{{ $profesional->id }}">{{ $profesional->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="servicio_id" class="form-label">💼 Seleccione el Servicio:</label>
                        <select class="form-select" id="servicio_id" name="servicio_id" required>
                            <option value="" selected disabled>-- Seleccione un servicio --</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->id }}">{{ $servicio->nombre }} - ${{ number_format($servicio->precio, 0) }} ({{ $servicio->duracion_minutos }} min)</option>
                            @endforeach
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
                            @forelse($reservas as $reserva)
                                <tr>
                                    <td><strong>#{{ $reserva->id }}</strong></td>
                                    <td>{{ $reserva->usuario->nombre }}</td>
                                    <td>{{ $reserva->profesional->nombre }}</td>
                                    <td>{{ $reserva->servicio->nombre }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($reserva->estado == 'activa')
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-danger">Cancelada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reserva->monto_reembolsado > 0)
                                            <span class="text-success font-weight-bold">${{ number_format($reserva->monto_reembolsado, 0) }}</span>
                                        @else
                                            <span class="text-muted">$0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($reserva->estado == 'activa')
                                            <form action="{{ url('/cancelar/'.$reserva->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de cancelar esta reserva?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">❌ Cancelar</button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>Ninguna</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No hay ninguna reserva registrada en el sistema todavía.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


@endsection