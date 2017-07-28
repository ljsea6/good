<header class="header-top navbar">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle side-nav-toggle">
            <span class="sr-only">
                Toggle navigation
            </span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand">
            <div><img  style="width: 15px" src="{{ asset('img/r_icono.png') }}"></div>
        </a>
        <ul class="nav navbar-nav-xs">
            <li>
                <a class="font-lg collapse" data-toggle="collapse" data-target="#headerNavbarCollapse">
                    <i class="icon-user move-d-1">
                    </i>
                </a>
            </li>
        </ul>
    </div>
    <div class="collapse navbar-collapse" id="headerNavbarCollapse">
        <ul class="nav navbar-nav">
            <li class="hidden-xs">
                <a class="font-lg sidenav-size-toggle">
                    <i class="icon-indent-decrease inline-block">
                    </i>
                </a>
            </li>
<!--             <li class="dropdown">
              <a href="#" class="dropdown-toggle font-lg" data-toggle="dropdown">
                <i class="icon-drawer2 font-12-xs"></i>
                <span class="badge bg-danger">3</span>
                <span class="hidden-sm hidden-md hidden-lg font-12 m-l-5">Recogidas pendientes</span>
              </a>
              <ul class="dropdown-menu dropdown-animated pop-effect dropdown-lg list-group-dropdown">
                <li class="no-link font-12">Tienes 3 recogidas pendientes</li>
                <li>
                  <a href="#">
                    <div class="user-list-wrap">
                      <div class="detail">
                        <span class="text-normal">Bancolombia</span>
                        <span class="time">Hace 2 dia</span>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <div class="user-list-wrap">
                      <div class="detail">
                        <span class="text-normal">ComfaCesar</span>
                        <span class="time">Hace 3 dias</span>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <div class="user-list-wrap">
                      <div class="detail">
                        <span class="text-normal">BBVA</span>
                        <span class="time">Ayer</span>
                      </div>
                    </div>
                  </a>
                </li>
                <li><a href="#" class="text-center">Ver todas</a></li>
              </ul>
            </li> -->
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="user-profile dropdown">
                <a href="#" class="clearfix dropdown-toggle" data-toggle="dropdown">
                    @if (currentUser()->avatar !== NULL)
                        <img src="{{ asset(currentUser()->avatar) }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                    @else
                        <img src="{{ asset("img/avatar-bg.png") }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                    @endif
                    <div class="user-name">
                        {{ currentUser()->nombre_completo }}
                        <span class="caret m-l-5">
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-animated pop-effect" role="menu">
                    <li>
                        <a href="{{ route('logout') }}">
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
            </li>
            <li class="hidden-xs">
                <!-- Button For Toggle Right Sidebar -->
                <a href="#" class="font-lg toggle-right-sidebar"><i class="icon-menu2 m-d-1"></i></a>
            </li>
        </ul>

    </div>
</header>
