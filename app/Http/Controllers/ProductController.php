<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ImageProd;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Event;

use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(){
        $products = Product::all();
        return view('productS.index', compact('products'));
    }

    public function getImages($productId){

        $product = Product::with('images')->find($productId);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $images = $product->images->map(function ($image) {
            return [
                'url' => asset('storage/' . $image->name), // Ajusta según cómo guardas las imágenes
                'description' => $image->description ??'',
            ];
        });

        return response()->json(['images' => $images]);
    }


    public function create(){
        $categories = Category::all(); // Obtiene todas las categorías
        return view('products.create', compact('categories'));
    }
    

    public function store(Request $request){
        $product = Product::create($request->only('category_id', 'name', 'description', 'price', 'status'));

        // Redirige a la vista con el ID del producto recién creado
        return redirect()->route('products.create')->with([
            'success' => 'Producto creado correctamente.',
            'prod_id' => $product->id
        ]);
    }

    public function edit($id){

        $product = Product::findOrFail($id);
        $categories = Category::all();

        return response()->json([
            'product' => $product,
            'categories' => $categories,
        ]);

    }

    /* public function update(Request $request, $id){


        $product = Product::findOrFail($id);

        $request->validate([
        
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
            
         ]);


       

        $product->update([ 
            
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
            
        ]);

        return redirect()->route('products.index')->with('success', 'Products update successfully');
    } */

    public function update(Request $request, string $id){

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

            try {
                // Buscar el evento a actualizar
                $event = Event::findOrFail($id);

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
                    'rooms' => $request->rooms,
                ]);

                // Enviar correo de confirmación de actualización
                $this->sendEmail($event);

                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado correctamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el evento: ' . $e->getMessage()
                ], 500);
            }
    }

    /* Actualizar imagen */
    public function uploadImage(Request $request){

        $request->validate([
            'file' => 'required|image', // Asegúrate de que sea un archivo de imagen
        ]);
    
        // Obtén el archivo subido
        $file = $request->file('file');
    
        // Obtén la extensión original
        $extension = $file->getClientOriginalExtension();
    
        // Crea un nombre único y aleatorio para el archivo con la extensión
       $filename = Str ::uuid()->toString() . '.' . $extension;
    
        // Almacena el archivo en el directorio 'products' dentro de 'public'
        $path = $file->storeAs('products', $filename, 'public');
    
        // Guarda el prefijo junto con el nombre del archivo en la base de datos
        ImageProd::create([
            'prod_id' => $request->input('prod_id'),
            'name' => 'products/' . $filename, // Agrega el prefijo 'products/' al nombre del archivo
        ]);
    
        return response()->json(['success' => 'Imagen subida correctamente.']);
    }
    


    public function destroy($id){

        $product = Product::findOrFail($id);

        // Eliminar el usuario
        $product->delete();
    
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);

    }

   


}
