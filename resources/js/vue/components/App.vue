<template>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow  ">
          
                    <div class=" text-center  p-3">
                        <img src="../../../../public/images/aisha.png" alt="Imagen Dashboard"
                            class="img-fluid mx-auto d-block" style="max-width: 100px;">
                        <h6 class="mt-3">Fundacion para el Desarrollo Educativo y Social de la Guajira</h6>
                      
                    </div>
                
                <ul class="menu-inner py-1">

                    <li class="menu-item">

                        <a class="menu-link">

                    <li class="nav-item">
                        <router-link active-class="active-menu" exact to="/home" class="nav-link"><i
                                class="fa fa-home fa-lg" aria-hidden="true"></i> Bienvenido </router-link>
                    </li>
                    </a>
                    </li>
                    <hr>
                    <!-- Layouts -->

                    <li class="menu-item " v-if="otro == 'Admin'">
                        <a href="" class="menu-link menu-toggle">

                            <div data-i18n="Layouts"><i class="fa fa-cog fa-lg" aria-hidden="true"></i> Parametros</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a class="menu-link" href="#" @click.prevent="users">
                                    <span>Usuario</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Procesos</span>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">

                            <div data-i18n="Account Settings"><i class="fa fa-graduation-cap fa-lg"
                                    aria-hidden="true"></i> Estudiante</div>
                        </a>
                        <ul class="menu-sub">
                           
                            <li class="menu-item">
                                <a class="menu-link" href="#" @click.prevent="estudiante">
                                    <span>Estudiante</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="menu-link" href="#" @click.prevent="notas">
                                    <span>Calificacion</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="menu-link" href="#" @click.prevent="abonos">
                                    <span>Pagos</span>
                                </a>
                            </li>
                           

                        </ul>
                    </li>

                    <!-- Components -->
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">Reportes</span></li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">

                            <div data-i18n="Account Settings"><i class="fa fa-cloud-download fa-lg"
                                    aria-hidden="true"></i> Reportes</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a class="menu-link">
                                    <router-link active-class="active-menu" exact to="/reporte/index">Paz y
                                        Salvo</router-link>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="menu-link">
                                    <router-link active-class="active-menu" exact to="/relacion">Informe
                                        Pagos</router-link>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">                <!-- Navbar -->
               
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                  
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                       
                    </div>
                    

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        
                              
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex ">
                            
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->

                            <li class="nav-item lh-1 me-3">

                            </li>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="../../../../public/assets/img/avatars/1.png"
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="../../../../public/assets/img/avatars/1.png"
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {{ }}
                                                    <span class="fw-semibold d-block" v-for="users in user"
                                                        :key="users.id">{{ users.name }}</span>
                                                    <small class="text-muted" v-for="use in user" :key="use.id">{{
                        use.username }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Mi Perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-cog me-2"></i>
                                            <router-link active-class="active-menu" exact to="/cambio/password">Cambiar
                                                Contraseña</router-link>
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click.prevent="salir()">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Salir</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User   <router-view></router-view> -->
                        </ul>
                    </div>
                </nav>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <router-view></router-view>
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

</template>

<script>
import axios from 'axios';


let users = document.head.querySelector('meta[name="user"]');
let otro = JSON.parse(users.content).especialidad;
let seccion = JSON.parse(users.content).id;

export default {
    data() {
        return {

            user: {},
            otro: [otro]
        }
    },
    mounted() {

        this.created();
        
    },

    methods: {
        users() {
            this.$router.push({ path: "/user/index" });
        },
        abonos() {
            this.$router.push({ path: "/abono/index" });
        },
        estudiante() {
            this.$router.push({ path: "/alumno/index" });
        },
        notas() {
            this.$router.push({ path: "/nota/index" });
        },
        reporte() {
            this.$router.push({ path: "/relacion" });
        },

        paz() {
            this.$router.push({ path: "/reporte/index" });
        },
        home() {
            this.$router.push({ path: "/home" });
        },
        async created() {
           
            axios.get('/users/' + seccion)
                .then(res => {
                    this.user = res.data.user;
                    this.home()

                    this.isLoading = false
                }).catch(error => {
                    this.users = [];
                    this.isLoading = false
                })
                //console.log(this.user)

        },
        salir() {
            axios.post('/logout')
                .then(res => {
                    toastr.warning('Session Cerrada');
                    location.reload();
                    return true;

                    this.isLoading = false
                }).catch(error => {
                    this.users = [];
                    this.isLoading = false
                })
        }

    },

}
</script>
<style>
a {
    text-decoration: none;
}
</style>