<template>
    <main class="main" id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12  ">
                    <div class="card  border ">
                        <div class="card-body  ">
                            <h6 align="center" style="background-color: tomato;" class=" p-2 border col-14 col-md-12 ">
                                Estudiantes</h6>

                            <div class="modal-body border p-2">
                                <div class="row">
                                    <div class="col-14  col-sm-2 input-group-sm mb-4">
                                        <label for="nameWithTitle" class="form-label">Tipo de Documento</label>
                                        <select v-model="tipo" class="form-select" id="exampleFormControlSelect2"
                                            aria-label="Multiple select example">
                                            <option Value="">Seleccione</option>
                                            <option value="CC">CC</option>
                                            <option value="TI">TI</option>
                                            <option value="PT">PT</option>
                                            <option value="CE">CE</option>
                                            <option value="AS">AS</option>

                                        </select>

                                    </div>
                                    <div class="col-14 col-sm-2 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">DOCUMENTO</label>
                                        <input type="text" id="nameWithTitle" class="form-control" v-model="documento">
                                    </div>

                                    <div class="col-14 col-sm-3 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">nombre y Apellidos</label>
                                        <input type="text" id="nameWithTitle" class="form-control" v-model="name">
                                    </div>
                                    <div class="col-14  col-sm-4 input-group-sm mb-4">
                                        <label for="nameWithTitle" class="form-label">email</label>
                                        <input type="email" id="nameWithTitle" class="form-control" v-model="correo">
                                    </div>

                                    <div class="col-14 col-sm-2 input-group-sm mb-4">
                                        <label for="nameWithTitle" class="form-label">direccion</label>
                                        <input type="text" id="nameWithTitle" class="form-control" v-model="direccion">
                                    </div>

                                    <div class="col-14  col-sm-2 input-group-sm mb-4">
                                        <label for="nameWithTitle" class="form-label">telefono</label>
                                        <input type="text" id="nameWithTitle" class="form-control" v-model="telefono">
                                    </div>



                                    <div class="col-14  col-sm-2 input-group-sm mb-4">
                                        <label for="nameWithTitle" class="form-label">Estado</label>
                                        <select v-model="estado" class="form-select" id="exampleFormControlSelect2"
                                            aria-label="Multiple select example">
                                            <option Value="">Seleccione</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>

                                        </select>

                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="d-flex justify-content-end gap-2 p-2">
                                <button @click.prevent="agregar(e)" v-if="btncrear" type="submit"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                                </button>
                                <button type="button" class="btn-sm btn btn-outline-info " v-if="btncrear"
                                    data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                    <i class="fa fa-search edu-search-pro" aria-hidden="true"></i> Listado</button>
                                <button @click.prevent="update()" v-if="btnedit" type="button"
                                    class="btn-sm btn btn-outline-info">
                                    <i class="fa fa-refresh" aria-hidden="true"></i> Actualizar
                                </button>
                            </div>

                        </div>
                    </div>

                    <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                        :is-full-page="fullPage" /><br>
                    <br>
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 align="center" style="background-color: tomato;"
                                        class=" p-2 border col-14 col-md-12 ">Estudiantes</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-12">
                                        <div class="card mb-4">

                                            <div class="card-body">

                                                <div class="form-floating">
                                                    <div class="table-responsive text-nowrap">
                                                        <table class="table table-bordered">
                                                            <thead>

                                                                <tr>
                                                                    <th>T.Identidad</th>
                                                                    <th>identidad</th>
                                                                    <th>nombre</th>
                                                                    <th>Telefono</th>
                                                                    <th>Estado</th>
                                                                    <th>Acciones</th>
                                                                </tr>

                                                            </thead>

                                                            <tbody class="table-border-bottom-0">
                                                                <tr>
                                                                    <th></th>

                                                                    <th>
                                                                        <div class="input-group-sm ">
                                                                            <input type="search" id="searchbox"
                                                                                class="form-control" v-model="names"
                                                                                @input="buscar()">
                                                                        </div>
                                                                    </th>

                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>

                                                                </tr>
                                                                <tr v-for="item in alumno" :key="item.id">
                                                                    <th>{{ item.tipo }}</th>
                                                                    <th>{{ item.documento }}</th>
                                                                    <th>{{ item.name }}</th>
                                                                    <th>{{ item.telefono }}</th>
                                                                    <th v-if="item.estado > 0"><span
                                                                            class="badge bg-label-primary me-1">Activo</span>
                                                                    </th>
                                                                    <th v-else v-bind:class="[errorClass]"><span
                                                                            class="badge bg-label-warning me-1">Inactivo</span>
                                                                    </th>
                                                                    <th>
                                                                        <button
                                                                            
                                                                            class="btn btn-success btn-sm"
                                                                            title="Editar" @click="abrirmodaledit(item)"
                                                                            style="margin-right: 5px;">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </button>
                                                                        <router-link @click="cerrarmodal()"
                                                                            :to="'/matricula/' + item.id"
                                                                            exact-active-class="active"
                                                                            class="btn btn-secondary btn-sm"> <i class="fa fa-external-link"></i>                                                                          
                                                                        </router-link>
                                                                    </th>                                                                  
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="demo-inline-spacing">
                                                            <nav aria-label="Page navigation">
                                                                <ul class="pagination pagination-sm">
                                                                    <li class="page-item prev"
                                                                        v-if="pagination.current_page > 1">
                                                                        <a class="page-link" href="#"
                                                                            @click.prevent="changePage(pagination.current_page - 1)">
                                                                            <i class="tf-icon bx bx-chevrons-left"
                                                                                height="80"></i>
                                                                        </a>
                                                                    </li>
                                                                    <li class="page-item" v-for="page in pagesNumber "
                                                                        :key="page"
                                                                        v-bind:class="[page === isActived ? 'active' : '']">
                                                                        <a class="page-link" href="#"
                                                                            @click.prevent="changePage(page)">{{ page
                                                                            }}</a>
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
                    <!---->

                </div>

            </div>
        </div>
    </main>
</template>

<script>

var archivo = document.getElementById("searchbox");
if (archivo) {

    archivo.addEventListener("input", (event) => {
        console.log(event.target.value)
    });
    archivo.addEventListener("search", (event) => {
        console.log('search X clicked')
    });

}

import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
export default {
    components: {
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {
            // id: this.$route.params.id,
            fullPage: '',
            alumno: {},
            alumnos: {},
            errors: {},
            direccion: '',
            documento: '',
            name: '',
            tipo: '',
            names: '',
            correo: '',
            telefono: '',
            estado: '',
            isLoading: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            btnedit: false,
            btncrear: false,
            onCancel: this.onCancel,
            id: '',
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
        validateForm(event) {

            if (!this.name) {
                toastr.warning('El nombre es obligatorio');
                event.preventDefault();
            } else
                if (!this.tipo) {
                    toastr.warning('El campo tipo de documento es obligatorio');
                    event.preventDefault();
                } else
                    if (!this.documento) {
                        toastr.warning('El campo documento es obligatorio');
                        event.preventDefault();
                    } else
                        if (!this.direccion) {
                            toastr.warning('El campo direccion es obligatorio');
                            event.preventDefault();
                        } else
                            if (!this.telefono) {
                                toastr.warning('El campo telefono es obligatorio');
                                event.preventDefault();
                            } else
                                if (!this.correo) {
                                    toastr.warning('El campo telefono es obligatorio');
                                    event.preventDefault();
                                } else
                                    if (!this.estado) {
                                        toastr.warning('El estado de la alumno es obligatorio');
                                        event.preventDefault();
                                    }



        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },
        cerrarmodal() {
            $('#staticBackdrop').modal('hide');
        },

        async created(page) {
            this.isLoading = true;
            axios.get('/alumno?page=' + page)
                .then(res => {
                    this.btncrear = true;
                    this.btnedit = false;
                    this.alumno = res.data.alumno.data;

                    this.pagination = res.data.pagination
                    this.isLoading = false
                    this.names = "";
                    this.name = '';
                    this.tipo = '';
                    this.documento = '';
                    this.telefono = '';
                    this.direccion = ''
                    this.correo = ''
                    this.estado = '';

                }).catch(error => {
                    toastr.warning('sesion caducada');
                    location.reload()
                    this.isLoading = false
                    return true;


                })

        },
        buscar() {
            if (this.names === "") {
                this.created()
            } else {
                this.isLoading = true;
                axios.post('/alumno/buscar', {
                    documento: this.names,
                })
                    .then((res) => {
                        this.alumno = res.data.alumno.data;

                        this.pagination = res.data.pagination
                        this.isLoading = false
                    }).catch((error) => {
                        toastr.warning('sesion caducada');
                        location.reload()
                        this.isLoading = false
                    })
            }
        },


        update() {
            this.validateForm();
            this.isLoading = true;
            axios.put('/alumno/' + this.id, {
                tipo: this.tipo,
                name: this.name,
                documento: this.documento,
                direccion: this.direccion,
                telefono: this.telefono,
                correo: this.correo,
                estado: this.estado,
            })
                .then(res => {
                    this.success = true;
                    if (res.data != 'no') {
                        toastr.success('Se Ha Actualizado Exitosamente');
                    } else {
                        toastr.warning('Ya esta alumno existe, Se actualizaron los demas campos');
                    }

                    this.name = '';
                    this.documento = '';
                    this.telefono = '';
                    this.direccion = ''
                    this.correo = ''
                    this.estado = '';
                    this.created();
                    this.isLoading = false

                }).catch((error) => {
                    toastr.warning('sesion caducada');
                    location.reload()
                })

        },
        agregar() {
            this.validateForm();
            this.isLoading = true;
            axios.post('/alumno', {
                name: this.name,
                tipo: this.tipo,
                documento: this.documento,
                direccion: this.direccion,
                telefono: this.telefono,
                correo: this.correo,
                estado: this.estado,

            })
                .then(res => {
                    this.success = true;
                    this.created();
                    if (res.data != 'no') {
                        toastr.success('Se Ha Guardado Exitosamente');
                    } else {
                        toastr.warning('Ya este estudiante esta registrado');
                    }


                    this.name = '';
                    this.documento = '';
                    this.telefono = '';
                    this.correo = ''
                    this.direccion = ''
                    this.estado = '';
                    this.isLoading = false

                }).catch((error) => {
                 //   toastr.warning('sesion caducada');
                  //  location.reload()
                })

            this.created();
        },
        abrilmodalcrear() {
            this.titulo = 'Aqui crearemos nuevas alumno';
            this.name = '';
            this.estado = '';
            this.btncrear = true;
            this.btnedit = false;

        },
        abrirmodaledit(datos) {
            this.name = datos.name;
            this.tipo = datos.tipo;
            this.documento = datos.documento;
            this.direccion = datos.direccion;
            this.telefono = datos.telefono;
            this.correo = datos.correo;
            this.estado = datos.estado;
            this.id = datos.id;
            this.btncrear = false;
            this.btnedit = true;
            $('#staticBackdrop').modal('hide');

        }

    }
}
</script>