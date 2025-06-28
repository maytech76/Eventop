<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define el modelo predeterminado para esta fábrica.
     */
    protected $model = Reservation::class;
     
     /* @return array<string, mixed>
     */
    public function definition(): array
    {
        // Se consulta a todos los IDs de usuarios con rol_id = 3 (usuarios)
        $userIds = User::where('rol_id', 3)->pluck('id')->toArray();

        // Se consulta a todos los IDs de usuarios con rol_id = 2 (consultores)
        $consultantIds = User::where('rol_id', 2)->pluck('id')->toArray();

        // Se Genera una fecha de reserva entre hoy y los próximos 30 días
        $reservationDate = $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d');

        // Se Genera una hora de inicio entre las 9:00 AM y las 3:00 PM
        $startTime = $this->faker->numberBetween(9, 15);
        // La hora de fin será una hora después de la hora de inicio
        $endTime = $startTime + 1;

        /*Se genera el Array con todos los campos necesarios para un registro de Reserva,
          Solo faltaria esperar el llamado desde el Seeder y recibir por parametro cuantos
          registros se deben generar*/
          
        return [
            'user_id' => $this->faker->randomElement($userIds), // Selecciona un ID de usuario aleatorio
            'consultant_id' => $this->faker->randomElement($consultantIds), // Selecciona un ID de consultor aleatorio
            'reservation_date' => $reservationDate, // Establece la fecha de la reserva
            'start_time' => sprintf('%02d:00', $startTime), // Formato de hora de inicio (HH:00)
            'end_time' => sprintf('%02d:00', $endTime), // Formato de hora de fin (HH:00)
            'status' => $this->faker->randomElement(['pendiente', 'confirmada', 'cancelada', 'finalizada']), // Genera un estado de reserva aleatorio
            'payment_status' => $this->faker->randomElement(['pendiente', 'pagado', 'fallido']), // Genera un estado de pago aleatorio
            'total_amount' => 50, // Monto total predeterminado (50 USD)
        ];
    }
}
