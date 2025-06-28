<div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
                     <div class="sticky">
                        <aside class="app-sidebar sidebar-scroll">

                            {{---------- Logo de Empresa ---------------}}
                            <div class="main-sidebar-header active">
                                <a class="desktop-logo logo-light active"href="{{route('dashboard')}}"><img src="{{asset('admin/img/brand/logo.png')}} " class="main-logo" alt="logo"></a>
                                <a class="desktop-logo logo-dark active" href="{{route('dashboard')}}"><img src="{{asset('admin/img/brand/logo-white.png')}} " class="main-logo" alt="logo"></a>
                                <a class="logo-icon mobile-logo icon-light active" href="{{route('dashboard')}}"><img src="{{asset('admin/img/brand/favicon.png')}} " alt="logo"></a>
                                <a class="logo-icon mobile-logo icon-dark active" href="{{route('dashboard')}} "><img src="{{asset('admin/img/brand/favicon-white.png')}} " alt="logo"></a>
                            </div>

                            <div class="main-sidemenu">

                                <div class="app-sidebar__user clearfix">
                                    <div class="dropdown user-pro-body">
                                        <div class="main-img-user avatar-xl">
                                            <img alt="user-img" src="{{asset('admin/img/users/6.jpg')}} "><span class="avatar-status profile-status bg-green"></span>
                                        </div>
                                        <div class="user-info">
                                            <h4 class="fw-semibold mt-3 mb-0">Usuario</h4>
                                            <span class="mb-0 text-muted">Admin</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>

                                {{-- Menu Administrador --}}
                                @if (Auth::user()->rol_id == 1)
                                <ul class="side-menu">

                                    <li class="side-item side-item-category">Administrativo</li>

                                        <li class="slide">
                                            <a class="side-menu__item" href="{{route('categories.index')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Categorias</span></a>
                                        </li>
                                        {{-- Eventos --}}
                                        <li class="slide">
                                            <a class="side-menu__item" href="{{route('events.index')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Eventos</span></a>
                                        </li>
                                        {{-- Invitados --}}
                                        <li class="slide">
                                            <a class="side-menu__item" href="{{route('guests.index')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Invitados * Event</span></a>
                                        </li>
                                        
                                        <li class="slide">
                                            <a class="side-menu__item" href="index.html"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Clientes</span></a>
                                        </li>
                                       
                                        <li class="slide">
                                            <a class="side-menu__item"  href="{{route('users')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Usuarios</span></a>
                                        </li>
                                        <li class="slide">
                                            <a class="side-menu__item"  href="{{route('reservations.index')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Check-In</span></a>
                                        </li>
                                        <li class="slide">
                                            <a class="side-menu__item"  href="{{route('reservations.calendario')}}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Check-Outs</span></a>
                                        </li>
                                        <li class="slide mb-2">
                                            <a class="side-menu__item" href="{{route('reservations.payment')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Ver Pagos</span></a>
                                        </li>

                                    <li class="side-item side-item-category">General</li>
                                        <li class="slide">
                                            <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"  viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3.5 4c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5zm-7 0c.83 0 1.5.67 1.5 1.5S9.33 11 8.5 11 7 10.33 7 9.5 7.67 8 8.5 8zm3.5 9.5c-2.33 0-4.32-1.45-5.12-3.5h1.67c.7 1.19 1.97 2 3.45 2s2.76-.81 3.45-2h1.67c-.8 2.05-2.79 3.5-5.12 3.5z" opacity=".3"/><circle cx="15.5" cy="9.5" r="1.5"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M12 16c-1.48 0-2.75-.81-3.45-2H6.88c.8 2.05 2.79 3.5 5.12 3.5s4.32-1.45 5.12-3.5h-1.67c-.69 1.19-1.97 2-3.45 2zm-.01-14C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg><span class="side-menu__label">Icons</span><i class="angle fe fe-chevron-down"></i></a>
                                            <ul class="slide-menu">
                                                <li class="side-menu__label1"><a href="javascript:void(0);">Icons</a></li>
                                                <li><a class="slide-item" href="icons.html">Font Awesome </a></li>
                                                <li><a class="slide-item" href="icons-2.html">Material Design Icons</a></li>
                                                <li><a class="slide-item" href="icons-3.html">Simple Line Icons</a></li>
                                                <li><a class="slide-item" href="icons-4.html">Feather Icons</a></li>
                                                <li><a class="slide-item" href="icons-5.html">Ionic Icons</a></li>
                                                <li><a class="slide-item" href="icons-6.html">Flag Icons</a></li>
                                                <li><a class="slide-item" href="icons-7.html">Pe7 Icons</a></li>
                                                <li><a class="slide-item" href="icons-8.html">Themify Icons</a></li>
                                                <li><a class="slide-item" href="icons-9.html">Typicons Icons</a></li>
                                                <li><a class="slide-item" href="icons-10.html">Weather Icons</a></li>
                                                <li><a class="slide-item" href="icons-11.html">Material Icons</a></li>
                                                <li><a class="slide-item" href="icons-12.html">Bootstrap Icons</a></li>
                                            </ul>
                                        </li>			
                                                
                                </ul>
                                @endif

                                {{-- Menu Asesor --}}
                                @if (Auth::user()->rol_id == 2)
                                <ul class="side-menu">
                                    <li class="side-item side-item-category">Asesor</li>
                                    <li class="slide">
                                        <a class="side-menu__item" href="{{route('asesor.calendario')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Calendario</span></a>
                                    </li>
                                </ul>
                                @endif

                                @if (Auth::user()->rol_id == 3)

                                 <ul class="side-menu">
                                    <li class="side-item side-item-category">Cliente</li>
                                        
                                        <li class="slide mb-2">
                                            <a class="side-menu__item" href="{{route('cliente.reservas')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Reserva</span></a>
                                        </li>
                                        <li class="slide mb-2">
                                            <a class="side-menu__item" href="{{route('cliente.calendario')}} "><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">Calendario</span></a>
                                        </li>
                                       
                                    </li>
                                 </ul>
                                    
                                @endif

                                <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
                            </div>

                        </aside>
                    </div>