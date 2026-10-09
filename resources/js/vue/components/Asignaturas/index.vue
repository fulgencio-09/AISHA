<template>
    <main class="main" id="main">
        <div class="container-fluid">
            <div class="row">
                <!--  <div class="col-md-12">
                    <div class="card shadow mb-4 col-md-12 col-12" >
                        <div class="  text-white "  >

                            <h6 align="center" style="background-color: tomato;" class=" p-2 border col-14 col-md-12 ">
                                Información del Estudiante</h6>
                        </div>
                        <div class="card-body" v-for="datos in matriculass.data" :key="datos.id" style="font-size: small; display: block ">
                            <div class="mb-2">
                                <div class="col-12 col-md-12">T.I: {{ datos.tipo }}</div>
                                <div class="col-7"></div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="col-12 col-md-12">Documento: {{ datos.documento }}</div>
                                <div class="col-7"></div>
                            </div>
                            
                            <div class=" mb-2" >
                                <div class="col-12 col-md-12">Nombre: {{ datos.name  }}</div>
                                <div class="col-7"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card ">
                        <div class="card-body">
                            <div class="modal-header">

                                <h6 align="center" style="background-color: tomato;"
                                    class="text-white p-2 border col-14 col-md-12 ">Matriculas Academicas</h6>

                            </div>
                            <div class="modal-body border p-2">
                                <hr>
                                <div class="row">

                                    <div class="col-14 col-sm-2 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">AÑo lectivo</label>
                                        <select v-model="ano" class="form-select" aria-label="Multiple select example">
                                            <option value="">seleccione</option>
                                            <option v-for="itemb in date" :key="itemb" :value="itemb" v-text="itemb">
                                            </option>>

                                        </select>
                                    </div>
                                    <div class="col-14 col-sm-2 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">Periodo</label>
                                        <select v-model="periodo" class="form-select"
                                            aria-label="Multiple select example">
                                            <option value="">seleccione</option>
                                            <option value="1">Primer periodo anual</option>
                                            <option value="2">Segundo periodo anual</option>
                                        </select>
                                    </div>


                                    <div class="col-14  col-sm-3 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">Semestre</label>
                                        <select v-model="semestre" class="form-select" id="exampleFormControlSelect2"
                                            aria-label="Multiple select example">
                                            <option value="">Seleccione</option>
                                            <option v-for="itemb in matricula" :key="itemb.id" :value="itemb.matricula"
                                                v-text="itemb.matricula">
                                            </option>>


                                        </select>
                                    </div>


                                    <div class="col-14 col-sm-3 input-group-sm mb-4 ">
                                        <label for="nameWithTitle" class="form-label">V. total Matricula</label>

                                        <input type="text" name="canti" placeholder="$"
                                            class=" col-sm-10  col-14 form-control" id="canti" v-model="matriculas"
                                            @input="coma()">

                                    </div>
                                    <input type="hidden" name="nombre" placeholder="$"
                                        class=" col-sm-10  col-14 form-control" v-model="id_asignatura">

                                </div>

                            </div>
                            <div class="d-flex justify-content-end gap-2 p-2">
                                <button @click.prevent="agregar(e)" v-if="btncrear" type="submit"
                                    class="btn-sm btn btn-outline-success"><i class="fa fa-floppy-o"
                                        aria-hidden="true"></i> Guardar</button>
                                <button @click.prevent="update()" v-if="btnedit" type="button"
                                    class="btn-sm btn btn-outline-info">
                                    <i class="fa fa-refresh" aria-hidden="true"></i> Actualizar
                                </button>
                                <button @click.prevent="created()" v-if="btnedit" type="button"
                                    class="btn-sm btn btn-outline-danger"> <i
                                        class='bx bx-window-close bx-tada-hover'></i>
                                    Cancelar</button>

                            </div>
                        </div>
                    </div>
                </div> -->


                <div class="card shadow mb-4">
                    <!-- Título del Card -->
                    <div class="  text-white text-center p-2">
                        <h6 align="center" style="background-color: tomato;"
                            class="text-white p-2 border col-14 col-md-12 ">Matriculas Academicas</h6>
                    </div>

                    <!-- Contenido del Card -->
                    <div class="card-body" style="font-size: small;">

                        <!-- Sección: Información del Estudiante -->
                        <div v-for="datos in estudiante" :key="datos.id" class="mb-3">

                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>T.I:</strong> {{ datos.tipo }}</div>
                                <div class="col-sm-4"><strong>Documento:</strong> {{ datos.documento }}</div>
                                <div class="col-sm-4"><strong>Nombre:</strong> {{ datos.name }}</div>
                            </div>
                        </div>
                        <hr>
                        <!-- Sección: Matrícula Académica -->

                        <div class="row g-3 mb-3">
                            <!-- Año lectivo -->
                            <div class="col-14  col-sm-3 input-group-sm ">
                                <label class="form-label">Año lectivo</label>
                                <select v-model="ano" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option v-for="itemb in date" :key="itemb" :value="itemb" v-text="itemb"></option>
                                </select>
                            </div>

                            <!-- Periodo -->
                            <div class="col-14  col-sm-3 input-group-sm ">
                                <label class="form-label">Periodo</label>
                                <select v-model="periodo" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="1">Primer periodo anual</option>
                                    <option value="2">Segundo periodo anual</option>
                                </select>
                            </div>

                            <!-- Semestre -->
                            <div class="col-14  col-sm-3 input-group-sm ">
                                <label class="form-label">Semestre</label>
                                <select v-model="semestre" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="1">Introductorio</option>
                                    <option value="2">Primer Semestre</option>
                                    <option value="3">Segundo Semestre</option>
                                    <option value="4">Tercer Semestre</option>
                                    <option value="5">Cuarto Semestre</option>
                                </select>
                            </div>
                            <div class="col-14  col-sm-3 input-group-sm ">
                                <label class="form-label">Sede</label>
                                <select v-model="sede" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="Riohacha">Riohacha</option>
                                    <option value="Fonseca">Fonseca</option>
                                </select>
                            </div>
                            <!-- Valor total matrícula -->
                            <div class="col-14  col-sm-3 input-group-sm ">
                                <label class="form-label">V. total Matrícula</label>
                                <input type="text" class="form-control" placeholder="$" :value="precioFormateado"
                                    @input="actualizarPrecio" />
                            </div>

                            <!-- Oculto -->
                            <input type="hidden" v-model="id_asignatura" />
                            <input type="hidden" v-model="materia" />
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <button v-if="btncrear" @click.prevent="agregar(e)" class="btn btn-outline-success btn-sm">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                            <button v-if="btnedit" @click.prevent="update()" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-refresh"></i> Actualizar
                            </button>
                            <button v-if="btnedit" @click.prevent="created()" class="btn btn-outline-danger btn-sm">
                                <i class="bx bx-window-close bx-tada-hover"></i> Cancelar
                            </button>
                        </div>

                    </div>
                </div>

                <div class="card col-md-12 mt-2">
                    <div class="card-body">

                        <h6 align="center" style="background-color: tomato;"
                            class=" text-white p-2 border col-14 col-md-12 ">
                            Matriculas Anteriores</h6>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Año</th>
                                        <th>Semestre</th>
                                        <th>periodo</th>
                                        <th>Saldo Matricula</th>
                                        <th>Saldo Pendiente</th>
                                        <th>Acciones</th>
                                    </tr>

                                </thead>

                                <tbody class="table-border-bottom-0">
                                    <tr v-for="item in matriculass.data" :key="item.id">
                                        <th>{{ item.sede }}</th>
                                        <th>{{ item.año }}</th>
                                        <th v-if="item.semestre == 1">Introductorio</th>
                                        <th v-else-if="item.semestre == 2">Primer semestre</th>
                                        <th v-else-if="item.semestre == 3">Segundo semestre</th>
                                        <th v-else-if="item.semestre == 4">Tercer semestre</th>
                                        <th v-else-if="item.semestre == 5">Cuarto semestre</th>
                                        <th v-else>Semestre no válido</th>
                                        <th>{{ item.periodo }}</th>
                                        <th>{{ formatoCOP(item.total) }}</th>
                                        <th>{{ formatoCOP(item.valor) }}</th>
                                        <th v-if="seccion == 'Admin'"><button @click="abrirmodaledit(item)"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fa fa-pencil"></i>
                                            </button>
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
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page
                                                }}</a>
                                        </li>

                                        <li class="page-item next"
                                            v-if="pagination.current_page < pagination.last_page">
                                            <a class="page-link" href="#"
                                                @click.prevent="changePage(pagination.current_page + 1)">
                                                <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                            </a>
                                        </li>

                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                    :is-full-page="fullPage" /><br>

            </div>

        </div>

    </main>
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
            seccion: [seccion],
            id: this.$route.params.id,
            fullPage: '',
            asignatura: {},
            date: {},
            matricula: {},
            matriculass: [],
            name: '',
            materia: 'No',
            errors: {},
            ano: '',
            sede: '',
            periodo: '',
            matriculas: '',
            id_asignatura: '',
            names: '',
            valor: '',
            semestre: '',
            estado: '',
            isLoading: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            btnedit: false,
            btncrear: false,
            onCancel: this.onCancel,
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
        total() {
            if (!Array.isArray(this.matriculass)) return 0;

            return this.matriculass.reduce((suma, item) => {
                const valor = Number(item.valor);
                return suma + (isNaN(valor) ? 0 : valor);
            }, 0);
        },
        precioFormateado() {
            return this.valor > 0
                ? '$ ' + new Intl.NumberFormat('es-CO').format(this.valor)
                : ''
        },
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
        this.created()

    },

    methods: {
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor)
        },
        actualizarPrecio(event) {
            const valorLimpio = event.target.value.replace(/[^\d]/g, '')
            this.valor = parseInt(valorLimpio) || 0
        },


        created() {
            //$('#staticBackdrop').modal('hidden');
            this.isLoading = true;
            axios.get('/matricula/' + this.id + '/edit')
                .then(response => {
                    this.matriculass = response.data.matriculass;
                    this.matricula = response.data.matricula;
                    console.log(this.matricula)
                    this.estudiante = response.data.estudiante;
                    this.date = response.data.date;

                    this.btncrear = true;
                    this.btnedit = false;
                    this.ano = ""
                    this.matriculas = ""
                    this.estado = ""
                    this.sede = ""
                    this.valor = ""
                    this.semestre = ""
                    this.periodo = "";
                    this.isLoading = false

                }).catch(error => {
                    this.errors = error.response.data.errors;
                    this.isLoading = false
                })

        },

        validateForm(event) {






        },
        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },
        abrirmodaledit(datos) {
            //  console.log(datos)
            if (datos.valor != datos.total) {
                toastr.warning('El valor de la matricula no se puede modificar porque ya hubo abonos realizados');
                return true;

            } else {
                this.btncrear = false;
                this.btnedit = true;
                this.id = datos.id;
                this.semestre = datos.semestre;
                this.periodo = datos.periodo;
                this.valor = datos.total;
                this.ano = datos.año;
                this.sede = datos.sede;
                this.id_asignatura = datos.id_asignatura
            }

        },

        agregar() {
            //  console.log(this.id)
            if (!this.ano) {
                toastr.warning('El campo año es obligatorio');
                return true;
            } else

                if (!this.valor) {
                    toastr.warning('El valor de la matricula es obligatorio');
                    return true;
                }
                else
                    if (!this.semestre) {
                        toastr.warning('El campo semestre es obligatorio');
                        return true;
                    }
                    else

                        if (!this.sede) {
                            toastr.warning('El campo sede es obligatorio');
                            return true;
                        } else
                            if (!this.periodo) {
                                toastr.warning('El periodo es obligatorio');
                                return true;
                            }
            this.isLoading = true;
            axios.post('/matricula', {
                año: this.ano,
                estudiante_id: this.id,
                valor: this.valor,
                total: this.valor,
                mat_matr: this.materia,
                estado: this.estado = 1,
                sede: this.sede,
                semestre: this.semestre,

                periodo: this.periodo

            })
                .then(res => {
                    this.success = true;


                    if (res.data == 'no') {

                        toastr.warning('Ya este estudiante esta matriculado');
                    } else
                        if (res.data == 'debe') {
                            toastr.warning('El estudiante tiene deudas pendiente ');
                        } else {
                            toastr.success('Se Ha Matriculado Exitosamente');
                        }


                    this.created();
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
                    } else if (error.response.status === 500) {
                        this.errors = error.response.data.errors;
                    }
                })

            this.created();
        },

        update() {
            if (!this.ano) {
                toastr.warning('El año es obligatorio');
                return true;
            } else

                if (!this.valor) {
                    toastr.warning('El valor de la matricula es obligatorio');
                    return true;
                }
                else
                    if (!this.semestre) {
                        toastr.warning('El semestre es obligatorio');
                        return true;
                    } else
                        if (!this.periodo) {
                            toastr.warning('El periodo es obligatorio');
                            return true;
                        }
            this.isLoading = true;
            //console.log(this.id_asignatura)
            axios.put('/matricula/' + this.id_asignatura, {
                valor: this.valor,
                total: this.valor,
                semestre: this.semestre,
                sede: this.sede,
                periodo: this.periodo,
                estudiante_id: this.id,

            })
                .then(res => {


                    this.created();
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

        },


    },


}
</script>