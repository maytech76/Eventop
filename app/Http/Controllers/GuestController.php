<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use Illuminate\Http\Request;

/* Librerias PhpMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Carbon;


use Endroid\QrCode\Builder\Builder; // Para construir el QR (opcional)
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;




class GuestController extends Controller
{
    
    public function index(){

       // Obtener eventos con el conteo de invitados
       $events = Event::withCount([
        'guests',
        'guests as attended_guests_count' => function($query) {
            $query->where('is_attended', true);
        }

        ])
        ->orderBy('event_date', 'desc')
        ->paginate(20); //Cantidad de Card por pagina en la vista

        return view('guests.index', compact('events'));

        
    }

    /* Listado de invitados de un evento */
    public function eventGuests(Event $event){

        $guests = Guest::where('event_id', $event->id)
                ->orderBy('created_at', 'desc')
                ->paginate(500); //maximo de invitados expuesto en la tabla de invitados eventguest
        
        return view('guests.event_guests', compact('event', 'guests'));
    }


    
    public function create(){

        return view('guests.create');
    }

    
    public function store(Request $request){

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,NULL,id,event_id,'.$request->event_id,
        ]);

        // Crear invitado con datos básicos
        $guest = Guest::create([

            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'is_attended' => false,
            'email_sent'=> 1,
            'code_in' => 'EVT-' . uniqid()

        ]);

        // Enviar email de invitación
        $this->sendInvitationEmail($guest);

        return back()->with('success', 'Invitado registrado y notificación enviada');
       

    }


    private function sendInvitationEmail($guest){

        $event = Event::find($guest->event_id);
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP (igual a tu implementación)
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eventos@maydev.tech';
            $mail->Password = '@dmiN1321**';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('eventos@maydev.tech', 'Invitación a Evento');
            $mail->addAddress($guest->email);

            // Configuración del correo
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Invitación al Evento: ' . $event->title;

            // Generar HTML desde vista Blade
           // Generar HTML desde vista Blade
            $html = View::make('emails.guest_invitation', [
                'event' => $event,
                'guest' => $guest,
                'registrationLink' => route('guest.confirmation.form', $event->id) // Cambiado a usar route() con nombre
            ])->render();

            $mail->isHTML(true);
            $mail->Body = $html;

            $mail->send();
            Log::info('Email de invitación enviado a: ' . $guest->email);

        } catch (Exception $e) {
            Log::error('Error al enviar invitación: ' . $mail->ErrorInfo);
            // No detenemos el flujo aunque falle el email
        }
    }


    public function showConfirmationForm(Event $event){


        // Verificar si ya se ha confirmado la asistencia del invitado
        $guest = Guest::where('email', request('email'))->where('event_id', $event->id)->first();

        if ($guest && $guest->is_attended) {//si ya ha sido confirmada la asistencia del invitado
            return redirect()->route('guest.alreadyConfirmed', $event->id);//redirigir a esta vista
        }

        // Si no, mostrar el formulario
        return view('guests.confirmation_form', compact('event'));

        
    }
   


    public function processConfirmation(Request $request, Event $event){

        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'photo' => 'required|image|max:6000'
        ]);

        // Buscar invitado
        $guest = Guest::where('email', $validated['email'])
            ->where('event_id', $event->id)
            ->first();

        if (!$guest) {
            return back()->withErrors(['email' => 'Este correo no está invitado al evento.'])->withInput();
        }

        // Verificar si ya confirmó
        if ($guest->is_attended) {
            return redirect()->route('guest.already_confirmed', $event->id);
        }

        // Guardar foto
        $photoPath = $request->file('photo')->store('guest_photos', 'public');

        // Generar código único para el QR
        $qrCodeText = 'EVT-' . uniqid();

        // 🔄 Generar QR en PNG con endroid/qr-code
        $qrCode = QrCode::create($qrCodeText)
            ->setSize(300) // Tamaño del QR
            ->setMargin(10) // Margen
            ->setForegroundColor(new Color(0, 0, 0)) // Color del QR (RGB)
            ->setBackgroundColor(new Color(255, 255, 255)); // Fondo blanco

        $writer = new PngWriter();
        $qrResult = $writer->write($qrCode);

        // Guardar el QR en formato PNG en storage
        $qrPath = 'qr_codes/' . $qrCodeText . '.png';
        Storage::disk('public')->put($qrPath, $qrResult->getString());

        // Actualizar datos del invitado
        $guest->update([
            'phone' => $validated['phone'],
            'photo' => $photoPath,
            'is_attended' => true,
            'qr_code_path' => $qrPath,
            'code_in' => $qrCodeText,
        ]);

        $this->sendQrEmail($guest, $event);//ejecutamos el metodo SendQrEmail

        return redirect()->route('guest.thankYou', $event->id)->with('guest', $guest);
    }

 
   

    public function alreadyConfirmed(Event $event){

        return view('guests.already_confirmed', compact('event'));
    }



    private function sendQrEmail($guest, $event) {
        $mail = new PHPMailer(true);
    
        try {
            // Configurar SMTP (sin cambios)
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eventos@maydev.tech';
            $mail->Password = '@dmiN1321**';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Remitente y destinatario
            $mail->setFrom('eventos@maydev.tech', 'Confirmacion de Asistencia');
            $mail->addAddress($guest->email);
    
            // Asunto y cuerpo
            $mail->Subject = 'Tu codigo QR para el evento: ' . $event->title;
            $mail->isHTML(true);
            $mail->Body = view('emails.qr_confirmation', [
                'event' => $event,
                'guest' => $guest,
            ])->render();
    
            // ✅ Cambio clave: Adjuntar el QR como PNG (no SVG)
            $qrExtension = pathinfo($guest->qr_code_path, PATHINFO_EXTENSION); // Obtener extensión
            $mail->addAttachment(
                storage_path('app/public/' . $guest->qr_code_path),
                'codigo_qr.png' // Forzar nombre con extensión .png
            );
    
            $mail->send();
            Log::info("QR enviado a: {$guest->email}");
    
        } catch (Exception $e) {
            Log::error("Error al enviar QR: " . $mail->ErrorInfo);
        }
    }

    public function thankYou(){

        return view('guests.thank_you');
    }


    public function verifyGuest(Request $request){

        $request->validate([
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id',
        ]);

        $guest = Guest::where('email', $request->email)
                    ->where('event_id', $request->event_id)
                    ->first();

        if (!$guest) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($guest->is_attended) {
            return response()->json(['status' => 'already_confirmed']);
        }

        return response()->json(['status' => 'ok']);
    }

    //CheckIn de Invitados

    public function showCheckInScanner(Event $event){

        return view('checkin.scanner', compact('event'));
    }

    
    public function processCheckIn(Request $request){


        $request->validate([

            'code_in' => 'required',
            'event_id' => 'required|exists:events,id',
        ]);

        $guest = Guest::where('code_in', $request->code_in)
                        ->where('event_id', $request->event_id)
                        ->first();

        if (!$guest) {
            return response()->json(['status' => 'error', 'message' => 'Invitado no encontrado.']);
        }

        if ($guest->status === 'checkin') {
            return response()->json(['status' => 'already_checked', 'guest' => $guest]);
        }

        $guest->update(['status' => 'checkin']);

        return response()->json(['status' => 'success', 'guest' => $guest]);

    }


 
    public function show(string $id){

            try {
                $guest = Guest::findOrFail($id);
                
                return response()->json([
                    'success' => true,
                    'guest' => [
                        'id' => $guest->id,
                        'name' => $guest->name,
                        'last_name' => $guest->last_name,
                        'email' => $guest->email,
                        'phone' => $guest->phone,
                        'photo' => $guest->photo ? $guest->photo : null,
                        'is_attended' => $guest->is_attended,
                        'email_sent' => $guest->email_sent,
                        'checkin_time' => $guest->checkin_time, // si existe
                        'checkout_time' => $guest->checkout_time // si existe
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('Error al mostrar invitado: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cargar los datos del invitado'
                ], 500);
            }
    }

   
    public function edit(string $id){
                
            try {
                $guest = Guest::findOrFail($id);
                
                return response()->json([
                    'success' => true,

                    'guest' => [
                        'id' => $guest->id,
                        'name' => $guest->name,
                        'last_name' => $guest->last_name,
                        'email' => $guest->email,
                        'phone' => $guest->phone ? $guest->phone :null,
                        'is_attended' => $guest->is_attended,
                        'status' => $guest->status,
                        'email_sent' => $guest->email_sent,
                        'event_date' => $guest->event ? $guest->event->event_date : null,
                        'start_time' => $guest->event ? $guest->event->start_time : null,
                        'end_time' => $guest->event ? $guest->event->end_time : null
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('Error al editar invitado: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cargar los datos del invitado'
                ], 500);
            }
    }

    public function update(Request $request, string $id){

            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'nullable|email',
                    'is_attended' => 'required|boolean',
                    'status' => 'required|in:active,checkIn,CheckOut,retire',
                    'email_sent' => 'nullable|boolean',
                   /*  'event_date' => 'nullable|date',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable' */
                ]);

                $guest = Guest::findOrFail($id);
                $guest->update([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'is_attended' => $request->is_attended,
                    'status' => $request->status,
                    'email_sent' => $request->email_sent
                ]);

                // Opcional: Actualizar datos del evento si es necesario
               /*  if ($guest->event) {
                    $guest->event->update([
                        'event_date' => $request->event_date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time
                    ]);
                } */

                return response()->json([
                    'success' => true,
                    'message' => 'Invitado actualizado correctamente'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                Log::error('Error al actualizar invitado: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al actualizar el invitado'
                ], 500);
            }
    }

   
    // En GuestController.php
    public function destroy($id){
        
        Guest::find($id)->delete();
        return response()->json(['success' => true]);
    }
    
}
