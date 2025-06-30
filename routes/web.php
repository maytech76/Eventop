<?php

use App\Exports\ProductsExport;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Event2Controller; // Eventos para grupos
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Maatwebsite\Excel\Exporter;

use App\Http\Controllers\EventController; // Eventos para usuario UNICO
use App\Http\Controllers\Guest\ExportImportController;
use App\Http\Controllers\GuestController;


/* Livewire */
use App\Livewire\ProductImages;
use App\Livewire\Inventory;
use App\Livewire\AdjustStock;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;



//public access to photo this guest
Route::get('/storage/guest_photos/{filename}', function ($filename) {
    $path = storage_path('app/public/guest_photos/' . $filename);
    
    if (!File::exists($path)) {
        abort(404, 'La imagen no existe');
    }
    
    // Determinar el tipo MIME para la respuesta
    $mime = mime_content_type($path);
    
    return response()->file($path, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000', // Cache por 1 año
    ]);
})->name('storage.guest_photos');


//Public acces photo to QR
Route::get('/storage/qr_codes/{filename}', function ($filename) {
    $path = storage_path('app/public/qr_codes/' . $filename);
    
    if (!File::exists($path)) {
        abort(404, 'La imagen no existe');
    }
    
    // Determinar el tipo MIME para la respuesta
    $mime = mime_content_type($path);
    
    return response()->file($path, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000', // Cache por 1 año
    ]);
})->name('storage.qr_codes');




/* Acceso a formulario de Invitacion*/
Route::get('/guests/event/{event}/guests', [GuestController::class, 'guestsEvent'])->name('events.guests');

// Rutas para confirmación pública
Route::get('/confirmar-invitacion/{event}', [GuestController::class, 'showConfirmationForm'])->name('guest.confirmation.form'); // Nombre de ruta actualizado

Route::post('/confirmar-invitacion/{event}', [GuestController::class, 'processConfirmation'])->name('guest.confirmation.store');


//Vista de Agradecimiento por confirmar su asistencia al evento
Route::get('/gracias/{event}', [GuestController::class, 'thankYou'])->name('guest.thankYou');


// Ruta para cuando el invitado ya haya confirmado su asistencia
Route::get('/ya-confirmado/{event}', [GuestController::class, 'alreadyConfirmed'])->name('guest.already_confirmed');


Route::get('/api/verify-guest', [GuestController::class, 'verifyGuest']);


// Vista para escanear el QR
Route::get('/check-in/{event}', [GuestController::class, 'showCheckInScanner'])->name('checkin.scanner');

// Procesar el check-in por código
Route::post('/check-in/process', [GuestController::class, 'processCheckIn'])->name('checkin.process');

//Ruta para escanear el QR
Route::view('/scanner', 'scanner')->name('scanner');


/* Rutas para el modulo Productos y Dropzone */
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}/images', [ProductController::class, 'getImages']);
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/products/upload', [ProductController::class, 'uploadImage'])->name('products.upload');
Route::get('products/export/', [ProductsExport::class, 'productsexport'])->name('products.export');



Route::get('/register', [RolController::class, 'index'])->name('register');
Route::post('/register', [RolController::class, 'create'])->name('register.create');
Route::post('/register', [RolController::class, 'store'])->name('register.store'); 

Route::get('users', [UserController::class, 'index'])->name('users');

Route::post('user', [UserController::class, 'store'])->name('users.store'); 

Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

Route::put('users/{id}', [UserController::class, 'update'])->name('users.update'); 

Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('reservation/calendario', function(){
    return view('reservations.calendario');
})->name('reservations.calendario');

Route::get('admin/fullcalendar', [ReservationController::class, 'getAllReservations'])->name('administrador.fullcalendar');

Route::resource('reservations', ReservationController::class);

Route::post('reservation/cancel',[ReservationController::class,'cancel'])->name('reservations.cancel');

Route::get('reservation/payment', [ReservationController::class,'showPayments'])->name('reservations.payment');

/* ------ RUTAS PARA ASESORES ------- */
Route::get('/asesor/calendario',function(){
    return view('asesor.calendario');
    })->name('asesor.calendario');

Route::get('asesor/fullcalendar',[ReservationController::class,'getReservationsAsesor'])->name('asesor.fullcalendar');
/* ------ FINAL RUTAS ASESORES ------- */


/* ------ RUTAS PARA CLIENTES ------- */
/* Route::get('/cliente/pagos',[ReservationController::class,'showClientPayments'])->name('cliente.pago'); */

Route::get('/cliente/calendario',function(){
    return view('cliente.calendario');
    })->name('cliente.calendario');

Route::get('cliente/fullcalendar',[ReservationController::class,'getReservationsCliente'])->name('cliente.fullcalendar'); /* Visualizar Reserva en el calendario */

Route::get('cliente/reservas',[ReservationController::class,'indexcliente'])->name('cliente.reservas');/* Listado de reserva del cliente */

Route::post('cliente/storeCliente',[ReservationController::class,'storeCliente'])->name('cliente.storeCliente');/* Listado de reserva del cliente */

Route::post('/paypal',[ReservationController::class,'completePayment']);

Route::get('/test-time', function() {
    return [
        'timezone' => config('app.timezone'),
        'current_time' => now()->format('Y-m-d H:i:s'),
        'db_time' => DB::select(DB::raw('SELECT NOW() as now'))[0]->now
    ];
});

/* ------ FINAL RUTAS CLIENTES ------- */


Route::get('/', function () {
    return view('welcome');
    });

Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        
        Route::get('/admin/dashboard', function () {
            return view('/admin/dashboard');
        })->name('dashboard');

        Route::get('/admin/dashboard', [Dashboard::class, 'showDashboard'])->name('dashboard');

        /* Route::get('/admin/categories', function () {
            return view('admin/categories');
        })->name('category'); */

        Route::get('/admin/products', function () {
            return view('admin/products');
        })->name('product');

        Route::get('/admin/inventory', Inventory::class)->name('admin.inventory');

        Route::get('/admin/ajust-stock', AdjustStock::class)->name('admin.stock');

        Route::get('admin/products/images', ProductImages::class)->name('admin.products.images');
        
        /* Ruta para eventos por invitado UNICO */
        Route::resource('events', EventController::class); 

         /* Ruta para eventos guest + partnes GRUPAL */
        /* Route::resource('events2', Event2Controller::class); */
        Route::get('events2',[Event2Controller::class, 'index'])->name('events2.index' );
        Route::post('events2', [Event2Controller::class, 'store'])->name('events2.store');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

       
        /* List Guest for Events */
        Route::get('/guests/event/{event}/guests', [GuestController::class, 'guestsEvent'])->name('events.guests');

        Route::get('/events/{event}/details', [EventController::class, 'showDetails'])->name('events.details');

        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');

        Route::put('events/{id}', [EventController::class, 'update'])->name('events.update');

        Route::delete('events/{id}', [EventController::class, 'destroy'])->name('events.destroy');


        /* ----------------------------------------------- */

        Route::resource('guests', GuestController::class); 

        Route::get('/events/event/{event}/guests', [GuestController::class, 'eventGuests'])->name('events.guests');

        Route::get('/guests/{guest}/show', [GuestController::class, 'show'])->name('guests.show');

        Route::delete('/guests/{id}', [GuestController::class, 'destroy'])->name('guests.destroy');

   

        // Exportar (GET)
        Route::get('guests/export', [ExportImportController::class, 'export'])->name('guests.export');

        // Importar (POST)
        Route::post('guests/import', [ExportImportController::class, 'import'])->name('guests.import');

        // Envío masivo
        Route::post('/events/{event}/send-invitations', [EventController::class, 'sendMassInvitations'])
        ->name('events.send-mass-invitations');

    });
