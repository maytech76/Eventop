<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Event2;
use App\Models\Partner;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;


/* Librerias PhpMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Event2Controller extends Controller
{
   
    public function index(){

        // Obtener solo usuarios con rol_id = 2 (organizer)
        $users = User::where('rol_id', 2)->get();
        $categories = Category::all(); //optenemos todas las categorias*/
        $events= Event::where('type', 'grupo')->get();

        return view('events2.index', compact('events', 'users', 'categories'));
    }

   
    public function create(){

        // Obtener solo usuarios con rol_id = 2 (organizer)
        $users = User::where('rol_id', 2)->get();
        $categories = Category::where('type', 'grupo'); //optenemos todas las categorias de type grupos*/
       

        return view('events2.create', compact('users', 'categories'));
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
            'limit_partners' => 'required',
            'type'=> 'required',
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
            'limit_partners'=>$request->limit_partners,
            'type'=>$request->type,
            'rooms'=>$request->rooms,
            

        ]);

            $this->sendEmail($event); 

         /*  dd($event->toarray());  */

        return redirect()->route('events2.index')->with('success', 'Evento Creado Correctamente');
    }

    
    public function show(string $id)
    {
        //
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
            Log::info('Iniciando actualización del evento ' . $id);
            Log::info('Datos recibidos:', $request->all());

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
                'limit_partners' => 'required',
                'status' => 'required|string|in:reservado,activo,culminado,cancelado',
                'rooms' => 'required',
            ]);

            // Buscar el evento a actualizar
            $event = Event::findOrFail($id);
            Log::info('Evento encontrado:', $event->toArray());

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
                'limit_partners' => $request->limit_partners,
                'status' => $request->status,
                'rooms' => $request->rooms,
            ]);

             Log::info('Evento actualizado correctamente');

            return response()->json([
                'success' => true
            ]);

            return redirect()->route('events2.index')->with('success', 'Evento Editado Correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
                  Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar evento: ' . $e->getMessage());
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
                'limit_partners'=>$event->limit_partners,
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
}
