<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;


/* Librerias PhpMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Carbon;

use App\Mail\EventInvitationMail;
use Illuminate\Support\Facades\Mail;





class EventController extends Controller{
    
    

    public function index(){

        // Obtener solo usuarios con rol_id = 2 (organizer)
        $users = User::where('rol_id', 2)->get();
        $categories = Category::all(); //optenemos todas las categorias*/
        $events= Event::all();

        return view('events.index', compact('events', 'users', 'categories'));
        
    }

    
    public function create(){

        // Obtener solo usuarios con rol_id = 2 (organizer)
        $users = User::where('rol_id', 2)->get();
        $categories = Category::all(); //optenemos todas las categorias*/
       

        return view('events.create', compact('users', 'categories'));
    }

 
    public function store(Request $request){

        // Validación de los datos recibidos
        $request->validate([

            'user_id' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'title' => 'required',
            'description' => 'required',
            'address' => 'required',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'limit_guest' => 'required',
            'rooms' => 'required',
            
        ]);

        // Creación de la reserva post a validacion
        $event =Event::create([

            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'event_date' => $request->event_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'limit_guest'=>$request->limit_guest,
            'rooms'=>$request->rooms,
            

        ]);

        $this->sendEmail($event);

        /*  dd($event->toarray());  */

        return redirect()->route('events.index')->with('success', 'Evento Creado Correctamente');
    }

    
    public function show(string $id){

        // Buscar el evento con relaciones (usuario y categoría)
        $event = Event::with(['user', 'category'])
        ->findOrFail($id);

        // Contar invitados (asumiendo relación 'guests')
        $guestsCount = $event->guests()->count();

        return view('events.show', compact('event', 'guestsCount'));
    }

    public function showDetails(Event $event){

        $guestsCount = $event->guests()->count();
        
        return view('events.partials.details-modal', compact('event', 'guestsCount'))->render();
    }

    
    public function edit(string $id){

        try {
            $event = Event::with(['user', 'category'])->findOrFail($id);
            $users = User::where('rol_id', 2)->get();
            $categories = Category::all();

            return response()->json([
                'success' => true,
                'event' => $event,
                'users' => $users,
                'categories' => $categories
            ]);
            

        } catch (\Exception $e) {
            Log::error('Error en edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar los datos del evento: ' . $e->getMessage()
            ], 500);
        }
    }

   
    public function update(Request $request, string $id){

        try {
            \Illuminate\Support\Facades\Log::info('Iniciando actualización del evento ' . $id);
            \Illuminate\Support\Facades\Log::info('Datos recibidos:', $request->all());

            // Validación de los datos recibidos
            $request->validate([
                'user_id' => 'required',
                'category_id' => 'required',
                'price' => 'required',
                'title' => 'required',
                'description' => 'required',
                'address' => 'required',
                'event_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'limit_guest' => 'required',
                'status' => 'required|string|in:reservado,activo,culminado,cancelado',
                'rooms' => 'required',
            ]);

            // Buscar el evento a actualizar
            $event = Event::findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Evento encontrado:', $event->toArray());

            // Actualizar los datos del evento
            $event->update([
                'user_id' => $request->user_id,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'event_date' => $request->event_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'limit_guest' => $request->limit_guest,
                'status' => $request->status,
                'rooms' => $request->rooms,
            ]);

            \Illuminate\Support\Facades\Log::info('Evento actualizado correctamente');

            return response()->json([
                'success' => true
            ]);

            return redirect()->route('events.index')->with('success', 'Evento Creado Correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al actualizar evento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function destroy(string $id){

        try {
            $event = Event::findOrFail($id);
            $event->update(['status' => 'cancelado']);
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el evento: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método para enviar el correo de confirmación de la reserva
    public function sendEmail($event){

        $user = User::find($event->user_id);
        $category = Category::find($event->category_id);
        $event= Event::find($event->id);
        $correo = Event::find($event->user->mail);

        $mail = new PHPMailer(true);

        /* Validamos con un try catch */
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eventos@maydev.tech';
            $mail->Password = '@dmiN1321**';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('eventos@maydev.tech','Registro de Evento');
            $mail->addAddress($user->email);

            $mail->CharSet = 'UTF-8';

            $mail->Subject = 'Su Evento a sido registrado exitosamente...!'; /* Asunto */

            $html = View::make('emails.reserva',[ /* Parametros a enviar a la vista emails.reserva */

                'userName' => $user->name .' '. $user->last_name,
                'category' => $category->name,
                'title' => $event->title,
                'price' => $event->price,
                'description' => $event->description,
                'address' => $event->address,
                'event_date' => $event->event_date,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'limit_guest'=>$event->limit_guest,
                'rooms'=>$event->rooms,

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

    public function sendMassInvitations(Event $event){
        
        $guests = $event->guests()->where('email_sent', false)->get();

        foreach ($guests as $guest) {
            try {
                Mail::to($guest->email)->send(new EventInvitationMail($guest, $event));
                $guest->update(['email_sent' => true]);
                Log::info("Invitación enviada a: {$guest->email}");
            } catch (\Exception $e) {
                Log::error("Error al enviar a {$guest->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Invitaciones enviadas a invitados pendientes.');
    }
}
