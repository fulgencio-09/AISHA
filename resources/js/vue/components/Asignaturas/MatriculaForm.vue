<template>
    <main class="main" id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="card shadow mb-4">
                    <div class="text-white text-center p-2">
                        <h6 class="text-white p-2 border col-12" style="background-color: tomato;">
                            Matriculas Academicas
                        </h6>
                    </div>

                    <div class="card-body" style="font-size: small;">
                        <div v-for="datos in estudiante" :key="datos.id" class="mb-3">
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>T.I:</strong> {{ datos.tipo }}</div>
                                <div class="col-sm-4"><strong>Documento:</strong> {{ datos.documento }}</div>
                                <div class="col-sm-4"><strong>Nombre:</strong> {{ datos.name }}</div>
                            </div>
                        </div>
                        <hr>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">Año lectivo</label>
                                <select v-model="form.ano" class="form-select" :disabled="modoEdicion">
                                    <option value="">Seleccione</option>
                                    <option v-for="item in date" :key="item" :value="item">{{ item }}</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">Periodo</label>
                                <select v-model="form.periodo" class="form-select" :disabled="modoEdicion">
                                    <option value="">Seleccione</option>
                                    <option value="1">Primer periodo anual</option>
                                    <option value="2">Segundo periodo anual</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">Semestre</label>
                                <select v-model="form.semestre" class="form-select" :disabled="modoEdicion">
                                    <option value="">Seleccione</option>
                                    <option value="1">Introductorio</option>
                                    <option value="2">Primer Semestre</option>
                                    <option value="3">Segundo Semestre</option>
                                    <option value="4">Tercer Semestre</option>
                                    <option value="5">Cuarto Semestre</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">Sede</label>
                                <select v-model="form.sede" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="Riohacha">Riohacha</option>
                                    <option value="Fonseca">Fonseca</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">V. total Matrícula</label>
                                <input type="text" class="form-control" placeholder="$"
                                    :value="precioFormateado" @input="actualizarPrecio" />
                            </div>

                            <div class="col-12 col-sm-3 input-group-sm">
                                <label class="form-label">Valor Seguro Estudiantil <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="$"
                                    :value="seguroFormateado" @input="actualizarSeguro"
                                    :disabled="modoEdicion" />
                                <small class="text-muted">
                                    Se crea automáticamente como pendiente al guardar la matrícula.
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button v-if="!modoEdicion" @click.prevent="guardar" class="btn btn-outline-success btn-sm">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                            <button v-if="modoEdicion" @click.prevent="actualizar" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-refresh"></i> Actualizar
                            </button>
                            <button v-if="modoEdicion" @click.prevent="cancelarEdicion" class="btn btn-outline-danger btn-sm">
                                <i class="bx bx-window-close"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card col-md-12 mt-2">
                    <div class="card-body">
                        <h6 class="text-white p-2 border col-12" style="background-color: tomato;">
                            Matriculas Anteriores
                        </h6>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Año</th>
                                        <th>Semestre</th>
                                        <th>Periodo</th>
                                        <th>Saldo Matrícula</th>
                                        <th>Saldo Pendiente</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in matriculas.data" :key="item.id">
                                        <td>{{ item.sede }}</td>
                                        <td>{{ item.año }}</td>
                                        <td>{{ nombreSemestre(item.semestre) }}</td>
                                        <td>{{ item.periodo }}</td>
                                        <td>{{ formatoCOP(item.total) }}</td>
                                        <td>{{ formatoCOP(item.valor) }}</td>
                                        <td v-if="esAdmin">
                                            <button @click="editar(item)" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </td>
                                        <td v-else>-</td>
                                    </tr>
                                    <tr v-if="!matriculas.data || matriculas.data.length === 0">
                                        <td colspan="7" class="text-center text-muted">No hay matrículas registradas.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="pagination.last_page > 1" class="d-flex justify-content-center mt-3">
                            <nav aria-label="Paginación de matrículas">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1)">Anterior</a>
                                    </li>
                                    <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page)">{{ page }}</a>
                                    </li>
                                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1)">Siguiente</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <loading v-model:active="isLoading" :is-full-page="true" />
            </div>
        </div>
    </main>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
    components: { Loading },
    data() {
        return {
            id: this.$route.params.id,
            estudiante: [],
            date: [],
            matriculas: { data: [] },
            isLoading: false,
            esAdmin: false,
            modoEdicion: false,
            form: {
                id: null,
                ano: '',
                periodo: '',
                semestre: '',
                sede: '',
                valor: 0,
                seguro_valor: 0,
            },
            pagination: {
                total: 0,
                current_page: 1,
                per_page: 5,
                last_page: 1,
                from: 0,
                to: 0,
            },
        };
    },
    computed: {
        precioFormateado() {
            return this.form.valor > 0 ? '$ ' + new Intl.NumberFormat('es-CO').format(this.form.valor) : '';
        },
        seguroFormateado() {
            return this.form.seguro_valor > 0 ? '$ ' + new Intl.NumberFormat('es-CO').format(this.form.seguro_valor) : '';
        },
        paginas() {
            const pages = [];
            const last = this.pagination.last_page || 1;
            const current = this.pagination.current_page || 1;
            let start = Math.max(1, current - 2);
            let end = Math.min(last, start + 4);
            start = Math.max(1, end - 4);
            for (let page = start; page <= end; page++) pages.push(page);
            return pages;
        },
    },
    mounted() {
        this.cargar(1);
    },
    methods: {
        toastr(tipo, mensaje) {
            if (window.toastr && typeof window.toastr[tipo] === 'function') {
                window.toastr[tipo](mensaje);
            }
        },
        nombreSemestre(semestre) {
            const nombres = {
                1: 'Introductorio',
                2: 'Primer semestre',
                3: 'Segundo semestre',
                4: 'Tercer semestre',
                5: 'Cuarto semestre',
            };
            return nombres[semestre] || 'Semestre no válido';
        },
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
            }).format(Number(valor) || 0);
        },
        actualizarPrecio(event) {
            this.form.valor = parseInt(event.target.value.replace(/[^\d]/g, ''), 10) || 0;
        },
        actualizarSeguro(event) {
            this.form.seguro_valor = parseInt(event.target.value.replace(/[^\d]/g, ''), 10) || 0;
        },
        actualizarPaginacion(data) {
            this.pagination = {
                total: data.total || 0,
                current_page: data.current_page || 1,
                per_page: data.per_page || 5,
                last_page: data.last_page || 1,
                from: data.from || 0,
                to: data.to || 0,
            };
        },
        cargar(page = 1) {
            this.isLoading = true;
            axios.get('/matricula/' + this.id + '/edit', { params: { page } })
                .then(response => {
                    this.matriculas = response.data.matriculass || { data: [] };
                    this.estudiante = response.data.estudiante || [];
                    this.date = response.data.date || [];
                    this.actualizarPaginacion(this.matriculas);

                    const meta = document.head.querySelector('meta[name="user"]');
                    if (meta) {
                        try {
                            this.esAdmin = JSON.parse(meta.content).especialidad === 'Admin';
                        } catch (e) {
                            this.esAdmin = false;
                        }
                    }
                })
                .catch(error => {
                    this.toastr('error', error?.response?.data?.message || 'No fue posible cargar las matrículas.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        validar() {
            if (!this.form.ano || !this.form.periodo || !this.form.semestre || !this.form.sede) {
                this.toastr('warning', 'Debe completar año, período, semestre y sede.');
                return false;
            }
            if (!this.form.valor || this.form.valor <= 0) {
                this.toastr('warning', 'Debe ingresar el valor total de la matrícula.');
                return false;
            }
            if (!this.modoEdicion && (!this.form.seguro_valor || this.form.seguro_valor <= 0)) {
                this.toastr('warning', 'Debe ingresar el valor del seguro estudiantil.');
                return false;
            }
            return true;
        },
        guardar() {
            if (!this.validar()) return;

            this.isLoading = true;
            axios.post('/matricula', {
                estudiante_id: this.id,
                año: this.form.ano,
                periodo: this.form.periodo,
                semestre: this.form.semestre,
                sede: this.form.sede,
                total: this.form.valor,
                valor: this.form.valor,
                seguro_valor: this.form.seguro_valor,
            })
                .then(response => {
                    this.toastr('success', response.data.message || 'Matrícula y seguro registrados correctamente.');
                    this.limpiar();
                    this.cargar(1);
                })
                .catch(error => {
                    this.toastr('error', error?.response?.data?.message || 'No fue posible registrar la matrícula.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        editar(item) {
            this.modoEdicion = true;
            this.form.id = item.id_asignatura || item.id;
            this.form.ano = item.año;
            this.form.periodo = String(item.periodo);
            this.form.semestre = String(item.semestre);
            this.form.sede = item.sede;
            this.form.valor = Number(item.valor) || 0;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        actualizar() {
            if (!this.validar()) return;

            this.isLoading = true;
            axios.put('/matricula/' + this.form.id, {
                valor: this.form.valor,
                total: this.form.valor,
                semestre: this.form.semestre,
                sede: this.form.sede,
                periodo: this.form.periodo,
            })
                .then(() => {
                    this.toastr('success', 'Matrícula actualizada correctamente.');
                    this.limpiar();
                    this.cargar(this.pagination.current_page);
                })
                .catch(error => {
                    this.toastr('error', error?.response?.data?.message || 'No fue posible actualizar la matrícula.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        cancelarEdicion() {
            this.limpiar();
        },
        limpiar() {
            this.modoEdicion = false;
            this.form = {
                id: null,
                ano: '',
                periodo: '',
                semestre: '',
                sede: '',
                valor: 0,
                seguro_valor: 0,
            };
        },
        cambiarPagina(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.cargar(page);
        },
    },
};
</script>
