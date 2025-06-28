<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationDetail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

/* Librerias PhpMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Carbon;

/* Libreria Twilio */
use Twilio\Rest\Client;


class ReservationController extends Controller{

     //Listado de usuarios y consultores 
    public function index() {

        $users = User::all(); //optenemos todos los usuarios*/
        $consultants = User::where('rol_id', 2)->get();// optenemos a todos lo condultores con rol 2 */
        $reservations = Reservation::with(['user', 'consultant'])->get();

        return view('reservations.index', compact('reservations', 'users', 'consultants'));

    }

     // Método para mostrar las reservas del cliente autenticado
    public function indexcliente() {

            $userId = Auth::user()->id; // Obtener el ID del usuario autenticado
            $consultants = User::where('rol_id', 2)->get();
            $reservations = Reservation::where('user_id', $userId)->get(); // Obtener solo las reservas del usuario
            return view('cliente.index', compact('reservations', 'consultants'));
    }


    public function create() {

        //muestrame un listado de usuarios con el rol=3 donde el campo delete_att = Null
        $users = User::where('rol_id',3)->whereNull('deleted_at')->get();

        //mostrar un listado de usuarios con el rol=2 donde el campo delete_att = Null
        $consultants = User::where('rol_id',2)->whereNull('deleted_at')->get();

        return view('reservations.create', compact('users', 'consultants'));
    }


     // Método para mostrar la vista de creación de una reserva desde el lado del cliente
    public function createCliente() {
        
        $consultants = User::where('rol_id', 2)->whereNull('deleted_at')->get();
        return view('cliente.reserva', compact('consultants'));
    }

    public function store(Request $request){

            // Validación de los datos recibidos
            $request->validate([
                'user_id' => 'required',
                'consultant_id' => 'required',
                'reservation_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'status' => 'required',
                'payment_status' => 'required',
                'total_amount' => 'required|numeric|min:0',
            ]);
    
            // Creación de la reserva post a validacion
            $reservation =Reservation::create([

                'user_id' => $request->user_id,
                'consultant_id' => $request->consultant_id,
                'reservation_date' => $request->reservation_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'total_amount' => $request->total_amount,

            ]);

            $this->sendEmail($reservation);

            /* dd($reservation->toarray()); */

            return redirect()->route('reservations.index')->with('success', 'Reserva Creada Correctamente');
    }

    public function storeCliente(Request $request){

        // Validación de los datos recibidos
        $request->validate([

            'user_id' => 'required',
            'consultant_id' => 'required',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
            'payment_status' => 'required',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Creación de la reserva post a validacion
        $reservation =Reservation::create([

            'user_id' => $request->user_id,
            'consultant_id' => $request->consultant_id,
            'reservation_date' => $request->reservation_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'total_amount' => $request->total_amount,

        ]);

         // Ejecutamos el metodo sendCinfirmationEmail que ejecuta el Envío de correo de confirmación
         $this->sendEmail($reservation);

        /* dd($reservation->toarray()); */

        return redirect()->route('cliente.reservas')->with('success', 'Reserva Creada Correctamente');
    }

     // Método para mostrar el formulario de edición de una reserva
     public function edit(string $id) {

        // Encontrar la reserva por su ID
        $reservation = Reservation::findOrFail($id);

        //Formateamos la hora de inicio con Carbon
        $reservation->start_time = Carbon::parse($reservation->start_time)->format('H:i');

         //Formateamos la hora final con Carbon
        $reservation->end_time = Carbon::parse($reservation->end_time)->format('H:i');

        //Solicitamos los datos de los usuarios y consultores segun el rol_id
        $users = User::where('rol_id', 3)->whereNull('deleted_at')->get();
        $consultants = User::where('rol_id', 2)->whereNull('deleted_at')->get();


        //enviamos a la vista el objeto de la reserva y los datos de los usuarios y consultores
        return view('reservations.edit', compact('reservation', 'users', 'consultants'));
    }

     // Método para actualizar una reserva existente
    public function update(Request $request, string $id) {

        // Validación de los datos recibidos
        $request->validate([
            
            'user_id' => 'required',
            'consultant_id' => 'required',
            'reservation_date' => 'required',
            'start_time' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:15:00',
            'end_time' => 'required|date_format:H:i|before_or_equal:15:00',
            'status' => 'required|in:pendiente,confirmada,cancelada,finalizada',
            'payment_status' => 'required',
            'total_amount' => 'required',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        return redirect()->route('reservations.index')->with('success', 'Reserva actualizada correctamente');
    }


     // Método para cancelar una reserva
    public function cancel(Request $request) {

        // Validación de los datos
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'cancelation_reason' => 'required|string',
        ]);

        /* Almacenamos en la variable el ID de la reservacion selecionada */
        $reservation = Reservation::findOrFail($request->reservation_id);
        $reservation->status = 'cancelada'; // Cambia el estado a 'cancelada'
         
        // insertamos en el campo cancelation_reason el motivo de cancelacion
        $reservation->cancelation_reason = $request->cancelation_reason;

        //Salvamos los cambios
        $reservation->save();

        //Debolvemos un Json
        return response()->json([
            'success' => true,
            'message' => 'La reserva ha sido cancelada exitosamente',
        ]);
    }

     // Métodos para obtener todas las reservas y convertirlas en eventos de calendario (para consultores y clientes)
    public function getAllReservations(){

        $reservations = Reservation::all();

        $events = [];
        foreach($reservations as $reservation){

            $color = '#06bd03';
            $bordercolor = '#28a745';
            $textcolor = '#000'; // Texto blanco por defecto


            if($reservation->status === 'pendiente'){ //estilo para cuando son reservaciones pendiente

                $color = '#ffc107';
                $bordercolor = '#ffc107';

            }elseif($reservation->status === 'cancelada'){ // estilo para cuando la reservaciones estan cancelada

                $color = '#9b0303';
                $bordercolor = '#f6a6a6ca';
            }

            $events[] = [

                'title' => 'Reserva de '. $reservation->user->name .' '. $reservation->user->last_name .' con ' . $reservation->consultant->name .' '. $reservation->consultant->last_name,
                'start' => $reservation->reservation_date.'T'.$reservation->start_time,
                'end' => $reservation->reservation_date.'T'.$reservation->end_time,
                'backgroundColor' => $color,
                'borderColor' => $bordercolor,
                'extendedProps' => [
                'userName' => $reservation->user->name . ' ' . $reservation->user->last_name,
                'consultantName' => $reservation->consultant->name . ' ' . $reservation->consultant->last_name,
                'startTime' => $reservation->start_time,
                'endTime' => $reservation->end_time,
                'amountPaid' => $reservation->total_amount,
                ],
                
            ];
        }

        return response()->json($events);
    }


    public function getReservationsAsesor(){

        $consultantId = Auth::user()->id;

        $reservations = Reservation::where('consultant_id', $consultantId)->get();

        $events = [];

        foreach($reservations as $reservation){

            $color = '#06bd03';
            $bordercolor = '#28a745';
            $textcolor = '#000'; // Texto blanco por defecto


            if($reservation->status === 'pendiente'){ //estilo para cuando son reservaciones pendiente

                $color = '#ffc107';
                $bordercolor = '#ffc107';

            }elseif($reservation->status === 'cancelada'){ // estilo para cuando la reservaciones estan cancelada

                $color = '#9b0303';
                $bordercolor = '#f6a6a6ca';
            }

            $events[] = [

                'title' => 'Reserva con '. $reservation->user->name .' '. $reservation->user->last_name,
                'start' => $reservation->reservation_date.'T'.$reservation->start_time,
                'end' => $reservation->reservation_date.'T'.$reservation->end_time,
                'backgroundColor' => $color,
                'borderColor' => $bordercolor
                
            ];
        }

        return response()->json($events);
    }


    //Generamos la reservacion en el fullcalendar, pasando los datos via JSON
    public function getReservationsCliente(){

        //capturamos el id del usuario que inicio session
        $userId = Auth::user()->id;

        //consultamos las reservaciones realizadas por el usuario que inicio Session
        $reservations = Reservation::where('user_id',$userId)->get();

        $events = [];
        foreach($reservations as $reservation){

        $color = '#06bd03';
        $bordercolor = '#28a745';
        $textcolor = '#000'; // Texto blanco por defecto


        if($reservation->status === 'pendiente'){ //estilo para cuando son reservaciones pendiente

            $color = '#ffc107';
            $bordercolor = '#ffc107';

        }elseif($reservation->status === 'cancelada'){ // estilo para cuando la reservaciones estan cancelada

            $color = '#9b0303';
            $bordercolor = '#f6a6a6ca';
        }

        $events[] = [

            'title' => 'Reserva de '. $reservation->user->name .' '. $reservation->user->last_name .' con ' . $reservation->consultant->name .' '. $reservation->consultant->last_name,
            'start' => $reservation->reservation_date.'T'.$reservation->start_time,
            'end' => $reservation->reservation_date.'T'.$reservation->end_time,
            'backgroundColor' => $color,
            'borderColor' => $bordercolor,
            'extendedProps' => [
            'userName' => $reservation->user->name . ' ' . $reservation->user->last_name,
            'consultantName' => $reservation->consultant->name . ' ' . $reservation->consultant->last_name,
            'startTime' => $reservation->start_time,
            'endTime' => $reservation->end_time,
            'amountPaid' => $reservation->total_amount,
            ],
            
        ];
    }

      return response()->json($events);
    }


     // Método para completar el pago de la reserva y crear el registro de la reserva y detalles del pago
     public function completePayment(Request $request){

        $request->validate([
            'orderID' =>'required',
            'details' => 'required',
            'user_id' => 'required|exists:users,id',
            'consultant_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:15:00',
            'end_time' => 'required|date_format:H:i|before_or_equal:15:00',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $details =$request->details;
        $payment_status = $details['status'];

        if($payment_status === 'COMPLETED'){

            $reservation = Reservation::create([
                'user_id' => $request -> user_id,
                'consultant_id' => $request -> consultant_id,
                'reservation_date' => $request -> reservation_date,
                'start_time' => $request -> start_time,
                'end_time' => $request -> end_time,
                'status' => 'confirmada',
                'payment_status' => 'pagado',
                'total_amount' => $request -> total_amount,
            ]);


            $transaction_id = $details['id'] ?? null;
            $payer_id = $details['payer']['payer_id'] ?? null;
            $payer_email = $details['payer']['email_address'] ?? null;
            $amount = $details['purchase_units'][0]['amount']['value'] ?? null;

            ReservationDetail::create([ //una vez creado los datos de la reserva se crea el registro de la reserva y detalles del pago

                'reservation_id' => $reservation->id,
                'transaction_id' => $transaction_id,
                'payer_id' =>  $payer_id,
                'payer_email' => $payer_email,
                'payment_status' => $payment_status,
                'amount' => $amount,
                'response_json' => json_encode($details),

            ]);

            $this->sendEmail($reservation);// enviar correo de confirmación de la reserva

            $user = User::find($request->user_id); //capturamos en $user  que hizo la reserva
            $userPhone = $user->phone; //Capturamos el $userPhone el telefono del usuario

            // Verificamos si el registro de usuario cuenta con datos en el campo phone
            if($userPhone){
                $this->sendWhastsAppMessage($userPhone, $this->generateWhatsAppMessage($reservation,$user));
            } 

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Pago no completado'], 400);
        }
    }



     // Método para enviar el correo de confirmación de la reserva
    public function sendEmail($reservation){

        $user = User::find($reservation->user_id);
        $consultant = User::find($reservation->consultant_id);

        $mail = new PHPMailer(true);

        /* Validamos con un try catch */
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@maydev.tech';
            $mail->Password = '@dmiN1321**';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('admin@maydev.tech','Reserva de Servicios');
            $mail->addAddress($user->email);

            $mail->CharSet = 'UTF-8';

            $mail->Subject = 'Confirmacion de Reserva - MayDev Spa'; /* Asunto */

            $html = View::make('emails.reserva',[ /* Parametros a enviar a la vista emails.reserva */

                'userName' => $user->name .' '. $user->last_name,
                'consultantName' => $consultant->name .' '. $consultant->Last_name,
                'reservationDate' => $reservation->reservation_date,
                'startTime' => $reservation->start_time,
                'endTime' => $reservation->end_time,
                'totalAmount' => $reservation->total_amount,

            ])->render();

            $mail->isHTML(true); /* Indicamos que acepte html */
            $mail->Body = $html; /* Indicamos que el cuerpo del correo es el html generado en la vista emails.reserva */

            $mail->send(); /* Enviamos el correo */

            return back()->with('success', 'Correo enviado correctamente.');

        } catch(Exception $e){
            Log::error('Error al enviar el correo: '. $mail->ErrorInfo);
            return back()->with('error','Error al enviar el correo :' . $mail->ErrorInfo);
        }
    }


    
     // Método para generar el mensaje de confirmación de WhatsApp
    protected function generateWhatsAppMessage($reservation, $user){

        return "Hola {$user->name}"." "."{$user->last_name}, tu reserva ha sido confirmada.\n".
            "Fecha: {$reservation->reservation_date}\n".
            "Hora de Inicio: {$reservation->start_time}\n".
            "Hora de Fin: {$reservation->end_time}\n".
            "Costo Total: {$reservation->total_amount}\n".
            "Gracias por elegir nuestros servicios.\n".
            "Maydev Spa.\n";
    }


     // Método para enviar un mensaje de WhatsApp
    protected function sendWhastsAppMessage($to,$message){

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid,$token);

        $twilio->messages->create(
            "whatsapp:+{$to}",
            [
                'from' => env('TWILIO_WHATSAPP_FROM'),
                'body' => $message
            ]
        );
    }


    //Metodo para visualizar los pagos de las reservaciones
   public function showPayments(){

     /*consulta anticipada para obtener los datos de las reservaciones relacionadas con reservationDetail
      donde se encuentrar los pagos en la base de datos */
     $payments = ReservationDetail::with(['reservation.user', 'reservation.consultant'])->get();

     //La informacion optenida se muestra en la vista reservation/payments.blade.php
    return view('reservations.payment', compact('payments'));


   }




}
