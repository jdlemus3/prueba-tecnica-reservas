<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Profesional;
use App\Models\Servicio;
use App\Models\Reserva;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function formularioCrear()
    {
        $usuarios = Usuario::all();
        $profesionales = Profesional::all();
        $servicios = Servicio::all();

       // Traemos todas las reservas cargando sus relaciones en español
    $reservas = Reserva::with(['usuario', 'profesional', 'servicio'])->orderBy('id', 'desc')->get();

    return view('reservas.crear', compact('usuarios', 'profesionales', 'servicios', 'reservas'));
    }
    
    
// 2. PROCESAR Y VALIDAR LA CREACIÓN 
    public function crear(Request $request)
    {
        // Definición fija de festivos Colombia 2026
        $festivos2026 = [
            '2026-01-01', '2026-01-12', '2026-03-23', '2026-04-02', 
            '2026-04-03', '2026-05-01', '2026-05-18', '2026-06-08', 
            '2026-06-15', '2026-06-29', '2026-07-20', '2026-08-07', 
            '2026-08-17', '2026-10-12', '2026-11-02', '2026-11-16', 
            '2026-12-08', '2026-12-25',
        ];

        $inicio = Carbon::parse($request->fecha_inicio, 'America/Bogota');
        $ahora = Carbon::now('America/Bogota');

        // VALIDACIÓN 1: Anticipación mínima de 2 horas
        if ($inicio->diffInHours($ahora, false) > -2) {
            return back()->withErrors('Las reservas deben hacerse con al menos 2 horas de anticipación.')->withInput();
        }

        // VALIDACIÓN 2: Horarios de operación (Lunes a Sábado de 7:00 a 19:00)
        if ($inicio->isSunday()) {
            return back()->withErrors('No se aceptan reservas los domingos. El horario es de Lunes a Sábado.')->withInput();
        }

        if ($inicio->hour < 7 || $inicio->hour >= 19) {
            return back()->withErrors('El horario de atención válido es de 7:00 a 19:00 (Hora de Bogotá).')->withInput();
        }

        // VALIDACIÓN 3: Validar si la fecha cae en un día festivo de Colombia
        if (in_array($inicio->format('Y-m-d'), $festivos2026)) {
            return back()->withErrors('No se aceptan reservas en días festivos de Colombia.')->withInput();
        }

        // VALIDACIÓN 4: Límite de 3 reservas activas por usuario
        $reservasActivas = Reserva::where('usuario_id', $request->usuario_id)
            ->where('estado', 'activa')
            ->where('fecha_inicio', '>', $ahora)
            ->count();

        if ($reservasActivas >= 3) {
            return back()->withErrors('El usuario seleccionado ya alcanzó el límite de 3 reservas activas independientes.')->withInput();
        }

        // VALIDACIÓN 5: Evitar solapamiento del mismo profesional
        $servicio = Servicio::find($request->servicio_id);
        // Forzamos con (int) a que el valor se transforme en un número entero puro
        $fin = $inicio->copy()->addMinutes((int) $servicio->duracion_minutos);

        $solapado = Reserva::where('profesional_id', $request->profesional_id)
            ->where('estado', 'activa')
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_inicio', [$inicio, $fin])
                      ->orWhereBetween('fecha_fin', [$inicio, $fin])
                      ->orWhere(function ($q) use ($inicio, $fin) {
                          $q->where('fecha_inicio', '<=', $inicio)
                            ->where('fecha_fin', '>=', $fin);
                      });
            })->exists();

        if ($solapado) {
            return back()->withErrors('El profesional seleccionado ya tiene otra cita activa que se cruza en ese rango de tiempo.')->withInput();
        }

        // SI PASA TODAS LAS REGLAS: Guardar registro
        $reserva = new Reserva();
        $reserva->usuario_id = $request->usuario_id;
        $reserva->profesional_id = $request->profesional_id;
        $reserva->servicio_id = $request->servicio_id;
        $reserva->fecha_inicio = $inicio;
        $reserva->fecha_fin = $fin;
        $reserva->estado = 'activa';
        $reserva->monto_reembolsado = 0.00;
        $reserva->save();

        return back()->with('exito', '¡Reserva registrada con éxito! ID de la reserva: #' . $reserva->id);
    }    
    
    
    
    
    
    

    public function cancelar($id)
{
    $reserva = Reserva::with(['usuario', 'servicio'])->findOrFail($id);
    
    $inicioReserva = Carbon::parse($reserva->fecha_inicio, 'America/Bogota');
    $ahora = Carbon::now('America/Bogota');
    
    // Calcular la diferencia en horas reales antes del evento
    // diffInHours devolverá un número positivo de cuántas horas faltan para la cita
    $horasAnticipacion = $ahora->diffInHours($inicioReserva, false);
    
    // LÓGICA DE REEMBOLSO:
    // Se reembolsa el 100% si se cancela con >= 24 horas de anticipación Y ADEMÁS:
    // El usuario es plan 'premium' O el servicio está marcado como reembolsable.
    if ($horasAnticipacion >= 24 && ($reserva->usuario->tipo_plan === 'premium' || $reserva->servicio->es_reembolsable == 1)) {
        $reserva->monto_reembolsado = $reserva->servicio->precio;
        $mensaje = 'La reserva ha sido cancelada con éxito. Se aplicó un reembolso total de $' . number_format($reserva->monto_reembolsado, 0) . ' por cumplir los términos.';
    } else {
        $reserva->monto_reembolsado = 0.00;
        $mensaje = 'La reserva ha sido cancelada. No aplica reembolso económico debido a las políticas de tiempo (< 24h) o restricciones del plan/servicio.';
    }
    
    // Cambiar estado a cancelada y guardar
    $reserva->estado = 'cancelada';
    $reserva->save();
    
    return back()->with('exito', $mensaje);
}
    
 
    
    
}