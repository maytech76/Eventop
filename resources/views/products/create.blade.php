@extends('admin.layouts.master')

@section('content')

<div class="col-lg-12 col-md-12">
    <div class="card m-4">
        <div class="card-body">
            <div class="main-content-label mg-b-5">
                Nuevo Producto
            </div>
            <p class="mg-b-20">Bienvenido al formulario asistido, registro de producto e imagenes</p>
            <div id="wizard1">
                <h3>Datos del Producto</h3>
                <div id="step1" class="wizard-step">
                    <form action="{{ route('products.store') }}" method="POST" class="col-12">
                        @csrf
                        <h2 class="d-none">Nombre</h2>
                        <div class="control-group form-group">
                            <label class="form-label">Categoria</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="disable">Selecione una Categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="control-group form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control required" placeholder="Name">
                        </div>
                        <div class="control-group form-group">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="description" id="description" class="form-control required"  placeholder="Agrega una descripcíon">
                        </div>
                        <div class="control-group form-group">
                            <label class="form-label">Precio de Venta</label>
                            <input type="number" name="price" id="price" class="form-control required" placeholder="Define el precio neto">
                        </div>
                        {{-- <div class="control-group form-group mb-0">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control required" placeholder="Address">
                        </div> --}}
                        <button type="submit" class="btn btn-success">Ingresar y continuar con las imagenes</button>
                    </form>
                </div>

            
                <hr class="my-4" style="border-top: 2px solid #ccc; border-radius: 5px;">

                <h3>Registro de Imagenes</h3>
                
                <div id="step2" class="wizard-step">
            
                    
                        <form action="{{ route('products.upload') }}" method="POST" class="dropzone" id="image-upload" enctype="multipart/form-data">
                            @csrf                     
                            <input type="hidden" name="prod_id" value="{{ session('prod_id') }}">
                        </form>
                 
                    
                </div>

                
                
            </div>
        </div>
    </div>
</div>

<!-- Incluye CSS y JS de Dropzone -->
{{-- @section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script> --}}
<script>
   
    Dropzone.options.imageUpload = {
        maxFilesize: 4, // MB
        acceptedFiles: 'image/*',
        success: function (file, response) {
            console.log(response.success);

            // Redirigir a la vista products/index tras el éxito
            window.location.href = "{{ route('products.index') }}";
        },
        error: function (file, response) {
            console.error(response);
            alert('Hubo un problema al subir la imagen. Inténtalo nuevamente.');
        }
    };
</script>

{{-- @endsection --}}
@endsection
