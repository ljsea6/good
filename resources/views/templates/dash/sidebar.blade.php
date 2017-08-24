<aside class="side-navigation-wrap">
    <div class="sidenav-inner">
        <ul class="side-nav magic-nav">
            <br>
            <li class="has-submenu">
                <a href="{{ route('admin.index') }}" class="animsition-link text-left">
                    <i class="fa fa-area-chart">
                    </i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="{{ route('admin.search') }}" class="animsition-link text-left">
                    <i class="fa fa-search">
                    </i>
                    <span class="nav-text">Buscar Referidos</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="{{ route('admin.network') }}" class="animsition-link text-left">
                    <i class="fa fa-sitemap">
                    </i>
                    <span class="nav-text">Referidos</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="#send" data-toggle="collapse" aria-expanded="false" class="text-left">
                    <i class="fa fa-folder-open">
                    </i>
                    <span class="nav-text">Invitaciones</span>
                </a>
                <div class="sub-menu collapse secondary list-style-circle" id="send">
                    <ul>
                        <li>
                            <a href="{{ route('admin.send.mail') }}" class="text-left">
                                <i class="fa fa-envelope-o">
                                </i>
                                Enviar Email
                            </a>
                        </li>
                       
                    </ul>
                </div>
            </li>
            @permission('configuracion')
            <li class="has-submenu">
                <a href="{{ route('admin.networks.index') }}" class="text-left">
                    <i class="fa fa-share-alt">
                    </i>
                    <span class="nav-text">Redes</span>
                </a>
                
            </li>

            <li class="has-submenu">
                <a href="{{ route('admin.reglas.index') }}" class="text-left">
                    <i class="fa fa-gavel">
                    </i>
                    <span class="nav-text">Reglas</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="#reportes" data-toggle="collapse" aria-expanded="false" class="text-left">
                    <i class="fa fa-bar-chart">
                    </i>
                    <span class="nav-text">Reportes</span>
                </a>
                <div class="sub-menu collapse secondary list-style-circle" id="reportes">
                    <ul>
                        <li>
                            <a href="{{ route('admin.reportes.index') }}" class="text-left">
                                <i class="fa fa-money">
                                </i>
                                Referidos
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <a href="{{ route('admin.reportes.codes') }}" class="text-left">
                                <i class="fa fa-money">
                                </i>
                                Código
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            @endpermission('configuracion')
            @permission('configuracion')
            <!-- <li class="has-submenu">
                <a href="#submenu5" data-toggle="collapse" aria-expanded="false" class="text-left">
                    <i class="icon-stats-bars">
                    </i>
                    <span class="nav-text">
                        Reportes
                    </span>
                </a>
                <div class="sub-menu collapse secondary list-style-circle" id="submenu5">
                    <ul>
                        @permission('reporte.entregas.devoluciones')                    
                        <li>
                            <a href="{{ route('admin.reportes.index') }}" class="text-left">
                                <i class="fa fa-truck">
                                </i>
                                <span>
                                    Entregas y devoluciones
                                </span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </div>
            </li>
            <li class="has-submenu">
                <a href="#submenu4" data-toggle="collapse" aria-expanded="false" class="text-left">
                    <i class="fa fa-archive">
                    </i>
                    <span class="nav-text">
                        Operativo
                    </span>
                </a>
                <div class="sub-menu collapse secondary list-style-circle" id="submenu4">
                    <ul>
                        <li>
                            <a href="{{ route('admin.recogidas.index') }}" class="text-left">
                                <i class="fa fa-truck">
                                </i>
                                <span>
                                    Liquidar
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>-->
            @endpermission('configuracion')
            @permission('configuracion')                    
            <li class="has-submenu">
                <a href="#submenu1" data-toggle="collapse" aria-expanded="false" class="text-left">
                    <i class="fa fa-gears">
                    </i>
                    <span class="nav-text">
                        Configuración
                    </span>
                </a>
                <div class="sub-menu collapse secondary list-style-circle" id="submenu1">
                    <ul>
                        <li>
                            <a href="{{ route('admin.usuarios.index') }}" class="text-left">
                                <i class="fa fa-user">
                                </i>
                                Usuarios
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.terceros.index') }}" class="text-left">
                                <i class="fa fa-user">
                                </i>
                                Terceros
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.proveedores.index') }}" class="text-left">
                                <i class="fa fa-user">
                                </i>
                                Proveedores
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.perfiles.index') }}" class="text-left">
                                <i class="fa fa-unlock">
                                </i>
                                Roles
                            </a>
                        </li>
                        <li>
                        <a href="{{ route('admin.dominios.index') }}" class="text-left">
                            <i class="fa fa-share-alt">
                            </i>
                            Dominios
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ciudades.index') }}" class="text-left">
                            <i class="fa fa-bank">
                            </i>
                            Ciudades
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.products.index')}}" class="text-left">
                            <i class="fa  fa-list">
                            </i>
                            Productos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.resoluciones.index') }}" class="text-left">
                            <i class="fa fa-sort-numeric-asc">
                            </i>
                            Resoluciones
                        </a>
                    </li>                   
                    <li>
                        <a href="{{ route('admin.oficinas.index') }}" class="text-left">
                            <i class="fa fa-building">
                            </i>
                            Oficinas
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-left">
                            <i class="fa fa-building">
                            </i>
                            Centros de negocio
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.comisiones.index') }}" class="text-left">
                            <i class="fa fa-money">
                            </i>
                            Comisiones
                        </a>
                    </li>
                    </ul>
                </div>
            </li>
            @endpermission
        </ul>
    </div>
</aside>
<aside class="right-sidebar-wrap">
          <ul class="sidebar-tab list-unstyled clearfix font-header font-11 bg-main">
            <li><a href="#sideTaskTab" data-toggle="tab" class="text-muted">Tareas</a></li>
            <li><a href="#sideAlertTab" data-toggle="tab" class="text-muted">Alertas</a></li>
          </ul>
          <div class="sidenav-inner">
            <div class="tab-content">
              <!-- Task Tab -->
              <div class="tab-pane fade" id="sideTaskTab">
                <div class="list-group font-12">
                  <a href="task.html" class="list-group-item">
                    Alerta
                    <div class="progress progress-striped progress-sm active m-t-5 no-m">
                      <div class="progress-bar progress-bar-success" style="width: 60%;"></div>
                    </div>
                  </a>
                  <a href="task.html" class="list-group-item">
                    Alerta
                    <span class="badge badge-info">31</span>
                  </a>
                </div>
              </div><!-- /.tab-pane -->
              
              <!-- Alert Tab -->
              <div class="tab-pane fade" id="sideAlertTab">
                <div class="content-wrap">
                  <div class="alert alert-warning alert-dismissible font-12 m-b-10" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    Reunion at 10:00 AM
                  </div>
                </div>
              </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
          </div>
</aside>