# Sistema de Gestión de Reservas IA - Prueba Técnica

**Desarrollador:** Juan David Lemus
**Enlace del proyecto:** https://prueba.softinginteractivo.com/public/

## 🚀 Descripción del Proyecto
Este sistema ha sido desarrollado bajo **Laravel 11** y **PHP 8.2**, diseñado para gestionar citas de manera inteligente, aplicando reglas de negocio estrictas para la asignación de espacios de tiempo.

## 🛠️ Arquitectura y Decisiones Técnicas
* **Base de Datos Manual:** Se estructuraron las tablas en español (`usuarios`, `servicios`, `profesionales`, `reservas`) directamente en phpMyAdmin, configurando relaciones inversas mediante Eloquent para mantener un código limpio.
* **Casting de Datos:** Se implementó un mapeo estricto de tipos de datos en los modelos para evitar errores de tipado con librerías de manejo de fechas (Carbon).
* **Front-end Local:** Para mitigar políticas restrictivas de CORS en el entorno de producción, se optó por compilar y servir los assets de Bootstrap 5 de forma local.

## 📋 Reglas de Negocio Implementadas (En Controlador)
1. **Anticipación:** Control de reservas con mínimo 2 horas de antelación.
2. **Calendario Laboral:** Bloqueo automático de domingos y días festivos oficiales de Colombia para el año 2026.
3. **Franja Horaria:** Restricción de citas por fuera del horario de 7:00 a 19:00.
4. **Límite de Usuario:** Máximo 3 reservas activas en simultáneo por cliente.
5. **Control de Solapamiento:** Validación de disponibilidad del profesional para evitar cruces de agenda.
6. **Política de Reembolsos:** Cálculo automatizado de devoluciones (100% del valor) basado en el tiempo de cancelación (mayor a 24 horas) y el tipo de plan del usuario (Premium) o naturaleza del servicio.