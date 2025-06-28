@extends('admin.layouts.master')

@section('content')


        {{-- Tabla de Productos--}}
        <div class="row row-sm mt-4 mx-auto">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Productos del Sistema</h3>
                        {{-- <button type="button" class="btn btn-success-gradient btn-w-xs mb-1"> + Usuario</button> --}}
                        <a class="btn ripple btn-teal" {{-- data-bs-target="#select2modal" data-bs-toggle="modal" --}} href="{{route('products.create')}} ">+ Nuevo</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table border-top-0  table-bordered text-nowrap border-bottom" id="responsive-datatable">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0 bg-light">ID</th>
                                        <th class="wd-15p border-bottom-0 bg-light">Nombres</th>
                                        <th class="wd-20p border-bottom-0 bg-light">Categoria</th>
                                        <th class="wd-20p border-bottom-0 bg-light">Precio</th>
                                        <th class="wd-25p border-bottom-0 bg-light">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>{{$product->id}} </td>
                                        <td class="fw-light">{{$product->name}}</td>
                                        <td class="fw-light">{{$product->category->name}}</td>
                                        <td class="fw-light">{{$product->price}}</td>
                                        
                                        <td>
                                        
                                            <!-- Otros botones -->
                                            <button class="btn btn-sm btn-primary" onclick="openImagesModal({{ $product->id }})">
                                                <span class="fe fe-image"></span>
                                            </button>
                                            
                                            <button id="bEdit" type="button" class="btn btn-sm btn-warning"  onclick="openEditModal({{ $product->id }})">
                                                <span class="fe fe-edit"></span>
                                            </button>
                                        
                                            <button id="bDel" type="button"  class="btn  btn-sm btn-danger" data-id="{{ $product->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal">
                                                <span class="fe fe-trash-2"> </span>
                                            </button>

                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Basic Registro -->
        <div class="modal" id="select2modal">
            <div class="modal-dialog" category="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Nuevo Registro</h6><button aria-label="Close" class="close"
                            data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col col-lg-12">
                        
                                <div class="m-2">
                                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group">
                                           
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Nombres">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Apellidos">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="phone" id="phone" class="form-control"  placeholder="Telefono">
                                        </div>

                                        <div class="form-group">
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Correo Eléctronico">
                                        </div>

                                        <div class="form-group">
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                        </div>
                                        
                                        <div class="modal-footer">
                                            <button class="btn ripple btn-success" type="submit">Registrar</button>
                                            <button class="btn ripple btn-danger" data-bs-dismiss="modal" type="button">Cerrar</button>
                                        </div>
                                    </form>
                                </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- End Basic modal -->

        {{-- Mostrar Imagenes --}}
        <div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagesModalLabel">Imágenes del Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="imagesContainer" class="row row-cols-1 row-cols-md-3 g-4">
                            <!-- Las imágenes se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        

        {{-- Modal editar --}}
        <div class="modal" id="openEditModal">
            <div class="modal-dialog" category="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Editar Producto</h6>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="{{route('products.update', $product->id)}}  " method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="editCategory">Categoria</label>
                                <select id="editCategory" name="category_id" class="form-control">
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editName">Nombres</label>
                                <input type="text" id="editName" name="name" value="{{$product->name}}" class="form-control">
                                <input type="text" id="editId" name="id" hidden value="{{$product->id}} ">
                            </div>
                            <div class="form-group">
                                <label for="editdescription">Descripcion</label>
                                <input type="text" id="editDescription" name="description" value="{{$product->description}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="editPrice">Precio</label>
                                <input type="text" id="editPrice" name="price" value="{{$product->price}}" class="form-control">
                            </div>
                             
                            <div class="modal-footer">
                                <button type="submit" class="btn ripple btn-success">Guardar</button>
                                <button class="btn ripple btn-danger" data-bs-dismiss="modal" type="button">Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Final Modal Editar --}}


       {{-- Modal Eliminar --}}
       <div class="modal fade" id="deleteModal" tabindex="-1" category="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" category="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Eliminar Registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p id="deleteMessage" class="text-normal">¿Estás seguro de que deseas eliminar este registro? Esta acción es inrreversible.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>

                </div>
            </div>
       </div>      
       {{-- Final Modal Eliminar --}}

@endsection

@push('scripts')

    {{-- Editar Registro --}}
    <script>
        function openEditModal(productId) {
            // Realiza una solicitud AJAX para obtener los datos del usuario
            fetch(`/products/${productId}/edit`)
                .then(response => response.json())
                .then(data => {
                    // Rellena el formulario con los datos obtenidos
                    const product = data.product;
                    const categories = data.categories;

                    document.getElementById('editForm').action = `/products/${product.id}`;
                    document.getElementById('editCategory').innerHTML = categories
                        .map(category => `<option value="${category.id}" ${category.id === product.category_id ? 'selected' : ''}>${category.name}</option>`)
                        .join('');
                    document.getElementById('editName').value = product.name;
                    document.getElementById('editDescription').value = product.description;
                    document.getElementById('editPrice').value = product.price;
                    
                    

                    // Muestra el modal
                    const editModal = new bootstrap.Modal(document.getElementById('openEditModal'));
                    editModal.show();
                })
                .catch(error => console.error('Error:', error));

                /* console.log(data); */
        }
    </script>

    {{-- Mostrar Imagenes en Modal --}}
    <script>
        function openImagesModal(productId) {
            fetch(`/products/${productId}/images`)
                .then(response => response.json())
                .then(data => {
                    const imagesContainer = document.getElementById('imagesContainer');
                    imagesContainer.innerHTML = ''; // Limpia cualquier contenido previo

                    if (data.images.length > 0) {
                        data.images.forEach(image => {
                            imagesContainer.innerHTML += `
                                <div class="col">
                                    <div class="card h-100">
                                        <img src="${image.url}" class="card-img-top" alt="Imagen del producto">
                                    </div>
                                </div>`;
                        });
                    } else {
                        imagesContainer.innerHTML = '<p class="text-center">No hay imágenes para este producto.</p>';
                    }

                    // Mostrar el modal
                    const imagesModal = new bootstrap.Modal(document.getElementById('imagesModal'));
                    imagesModal.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>


    {{-- Eliminar registro --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            let productIdToDelete = null;

            // Capturar el ID del usuario al abrir el modal
            document.querySelectorAll('#bDel').forEach(button => {
                button.addEventListener('click', function () {
                    productIdToDelete = this.getAttribute('data-id');
                    document.getElementById('deleteMessage').textContent = '¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.';
                });
            });

            // Confirmar y realizar la eliminación
            document.getElementById('confirmDelete').addEventListener('click', function () {
                if (productIdToDelete) {
                    fetch(`/products/${productIdToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (response.ok) {
                                document.getElementById('deleteMessage').textContent = 'El registro fue eliminado correctamente.';
                                
                                // Desactivar los botones del modal mientras se muestra el mensaje
                                document.getElementById('confirmDelete').disabled = true;
                                document.querySelector('[data-bs-dismiss="modal"]').disabled = true;

                                // Cerrar el modal después de 2 segundos
                                setTimeout(() => {
                                    const modalElement = document.getElementById('deleteModal');
                                    const modal = bootstrap.Modal.getInstance(modalElement);
                                    modal.hide();

                                    // Recargar la página después de cerrar el modal
                                    location.reload();
                                }, 2000);
                            } else {
                                document.getElementById('deleteMessage').textContent = 'Error al eliminar el registro. Por favor, inténtelo de nuevo.';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('deleteMessage').textContent = 'Ocurrió un error inesperado. Por favor, inténtelo más tarde.';
                        });
                }
            });
        });

    </script>


@endpush