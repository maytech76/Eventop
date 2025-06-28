<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){

        $categories = Category::all();
        return view('categories.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        $category = Category::create($request->only( 'name', 'description'));

        return redirect()->route('categories.index')->with([
            'success' => 'Producto creado correctamente.',
            'category_id' => $category->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){

        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
    
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){

        $category = Category::findOrFail($id);

        // Eliminar el usuario
        $category->delete();
    
        return response()->json(['message' => 'Categoria eliminada correctamente'], 200);
    }
}
