<template>
    <div class="content-wrapper" v-if="seccion == 'Admin'">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-xl-12">
                 
                    <div class="nav-align-top mb-4 ">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home-tab-pane" type="button" role="tab"
                                    aria-controls="home-tab-pane" aria-selected="true">Registro de Usuarios del
                                    Sistema</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-tab-pane" type="button" role="tab"
                                    aria-controls="profile-tab-pane" aria-selected="false">Usuarios Registrados</button>
                            </li>

                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel"
                                aria-labelledby="home-tab" tabindex="0">
                                <div class="col-sm-12 border mt-2">
                                    <label style="background-color: tomato;" class="col-md-12 col-12 border"
                                        align="center">Registro de
                                        Usuarios</label>
                                </div>
                                <div class="tab-pane  show active mt-2 border p-2" id="navs-top-home" role="tabpanel">
                                    <form v-on:submit.prevent="agregar()" method="POST">

                                        <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true"
                                            :on-cancel="onCancel" :is-full-page="fullPage" />


                                        <div class="row">

                                            <div class="col-sm-5 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Nombres y Apellidos</label>
                                                <input id="smallInput" class="form-control form-control-sm "
                                                    :required="name" type="text" name="name" v-model="name" />

                                            </div>
                                            <div class="col-sm-3 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Usuario</label>
                                                <input id="smallInput" class="form-control form-control-sm "
                                                    :required="usuario" type="text" name="username"
                                                    v-model="username" />

                                            </div>

                                            <div class="col-md-2 input-group-sm" id="partos">
                                                <label for="smallSelect" class="form-label">Estado</label>
                                                <select class="form-select form-select-sm" id="" v-model="estado">
                                                    <option value="">Seleccione</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>

                                                </select>
                                            </div>
                                            <div class="col-md-2 input-group-sm" id="partos">
                                                <label for="smallSelect" class="form-label">Roles</label>
                                                <select class="form-select form-select-sm" id="" v-model="especialidad">
                                                    <option value="">Seleccione</option>
                                                    <option value="Asistentes">Asistentes</option>
                                                    <option value="Admin">Administrador</option>

                                                </select>
                                            </div>
                                            <div class="col-sm-4 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label" name="email">Email</label>
                                                <input id="smallInput" class="form-control form-control-sm"
                                                    :required="email" type="email" name="email" v-model="email" />

                                            </div>

                                            <div class="col-sm-3 input-group-sm " id="input">
                                                <label for="smallInput" class="form-label">Password</label>
                                                <input class="form-control form-control-sm" :required="password"
                                                    type="password" id="password" name="password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password" v-model="password" />

                                            </div>
                                            <div class="col-sm-3 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Confirmar</label>
                                                <input class="form-control form-control-sm"
                                                    :required="password_confirmation" type="password"
                                                    name="password_confirmation"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password" v-model="password_confirmation" />

                                            </div>
                                            <div class="d-flex justify-content-end gap-2 p-2">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                                                </button>


                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel"
                                aria-labelledby="profile-tab" tabindex="0">
                                <div class="tab-pane " id="navs-top-profile" role="tabpanel">
                                    <div class="col-sm-12 border mt-2">
                                    <label style="background-color: tomato;" class="col-md-12 col-12 border "
                                        align="center">Tabla de Registros de Usuarios</label>
                                </div>
                                    <div class="card mt-1">

                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nombres</th>
                                                        <th>Usuario</th>
                                                        <th>Rol</th>
                                                        <th>Estado</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="itemm in user.data" :key="itemm.id">
                                                        <th>{{ itemm.name }}</th>
                                                        <th>{{ itemm.username }}</th>
                                                        <th>{{ itemm.especialidad }}</th>
                                                        <th v-if="itemm.estado >= 1"><span
                                                                class="badge bg-label-primary me-1">Activo</span>
                                                        </th>
                                                        <th v-else v-bind:class="[errorClass]"><span
                                                                class="badge bg-label-warning me-1">Inactivo</span>
                                                        </th>
                                                        <th>
                                                            <div class="dropdown">

                                                                <button type="button"
                                                                    class="btn p-0 dropdown-toggle hide-arrow"
                                                                    data-bs-toggle="dropdown">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <router-link :to="'/user/' + itemm.id"
                                                                        exact-active-class="active"
                                                                        class="dropdown-item">
                                                                        <a><i class="bx bx-edit-alt me-1"></i>
                                                                            Editar</a>
                                                                    </router-link>

                                                                </div>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-12">
                                            <div class="demo-inline-spacing">
                                                <nav aria-label="Page navigation">
                                                    <ul class="pagination pagination-sm">
                                                        <li class="page-item prev" v-if="pagination.current_page > 1">
                                                            <a class="page-link" href="#"
                                                                @click.prevent="changePage(pagination.current_page - 1)">
                                                                <i class="tf-icon bx bx-chevrons-left" height="80"></i>
                                                            </a>
                                                        </li>
                                                        <li class="page-item" v-for="page in pagesNumber " :key="page"
                                                            v-bind:class="[page === isActived ? 'active' : '']">
                                                            <a class="page-link" href="#"
                                                                @click.prevent="changePage(page)">{{ page }}</a>
                                                        </li>

                                                        <li class="page-item next"
                                                            v-if="pagination.current_page < pagination.last_page">
                                                            <a class="page-link" href="#"
                                                                @click.prevent="changePage(pagination.current_page + 1)">
                                                                <i class="fa fa-angle-double-right"
                                                                    aria-hidden="true"></i>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
let users = document.head.querySelector('meta[name="user"]');
let seccion = JSON.parse(users.content).especialidad;

export default {
    components: {
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {
            empresa: {},
            user: {},
            errors: {},
            seccion: [seccion],
            name: '',
            usuario: '',
            username: '',
            especialidad: '',
            onCancel: this.onCancel,
            email: '',
            estado: '',
            ips_id: '',
            password: '',
            password_confirmation: '',
            role: '',
            isLoading: false,
            fullPage: true,
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'last_page': 0,
                'to': 0,

            },
            offset: 2,
        }
    },
    computed: {
        isActived: function () {
            return this.pagination.current_page;
        },
        pagesNumber: function () {
            if (!this.pagination.to) {
                return [];
            }
            var from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2)
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    mounted() {

        this.created();

    },

    methods: {

        async created(page = 1) {
            this.isLoading = true;
            axios.get('/user?page=' + page)
                .then(res => {
                    this.user = res.data.user;
                    this.empresa = res.data.empresa;
                    this.pagination = res.data.pagination
                    this.isLoading = false
                }).catch(error => {
                    this.user = [];
                    this.isLoading = false
                })

        },

        eliminar(id) {
            /* axios.delete('/user/'+id)
                 .then(response => {
                     this.users = response.data;
                     this.isLoading = false
                 }).catch(error => {
                     this.users = []
                     this.isLoading = false
                 })*/
            this.isLoading = true;
            toastr.info('No se puede eliminar el usuario ');
            this.isLoading = false
            this.created();

        },
        agregar() {
            this.isLoading = true;
            axios.post('/user', {
                name: this.name,
                username: this.username,
                especialidad: this.especialidad,
                email: this.email,
                estado: this.estado,
                password: this.password,
                password_confirmation: this.password_confirmation,

            })
                .then(response => {
                    this.success = true;
                    toastr.success('Se Ha Guardado Exitosamente');
                    this.isLoading = false

                }).catch((error) => {

                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                        setTimeout(function () {
                            $(".alert").fadeOut(1500);
                        }, 5000);

                        setTimeout(function () {
                            $(".alert").fadeIn(1500);
                        }, 1000);
                        this.isLoading = false
                    }
                })

            this.name = '';
            this.username = '';
            this.email = '';
            this.ips_id = '';
            this.password = '';
            this.password_confirmation = '';
            this.created();
            this.getRol();
        }

    }
}
</script>
