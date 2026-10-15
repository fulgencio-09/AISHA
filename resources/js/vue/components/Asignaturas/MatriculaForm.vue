<template>
    <main class="main matricula-page" id="main">
        <div class="container-fluid">
            <div class="matricula-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1"><i class="fa fa-graduation-cap me-2"></i>Matriculas Académicas</h4>
                    <p class="text-muted mb-0">Gestión de matrículas académicas y valores asociados al estudiante.</p>
                </div>
                <button v-if="!modoEdicion" class="btn btn-primary" @click="cargar(1)">
                    <i class="fa fa-refresh me-1"></i> Actualizar
                </button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card matricula-stat border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Matrículas registradas</small>
                                <h3 class="mb-0">{{ pagination.total }}</h3>
                            </div>
                            <span class="matricula-icon bg-primary-subtle text-primary"><i class="fa fa-list"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card matricula-stat border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Estudiante</small>
                                <h6 class="mb-0 text-truncate" style="max-width: 240px;">{{ estudiante[0]?.name || 'Sin estudiante' }}</h6>
                            </div>
                            <span class="matricula-icon bg-info-subtle text-info"><i class="fa fa-user"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card matricula-stat border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Documento</small>
                                <h6 class="mb-0">{{ estudiante[0]?.documento || '—' }}</h6>
                            </div>
                            <span class="matricula-icon bg-success-subtle text-success"><i class="fa fa-id-card"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ modoEdicion ? 'Editar matrícula' : 'Nueva matrícula' }}</strong>
                        <div class="small text-muted">Complete la información académica y financiera.</div>
                    </div>
                    <span v-if="modoEdicion" class="badge bg-warning-subtle text-warning">Modo edición</span>
                </div>
                <div class="card-body">
                    <div class="matricula-student-box mb-4" v-if="estudiante.length">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Tipo de documento</small>
                                <strong>{{ estudiante[0].tipo || '—' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Documento</small>
                                <strong>{{ estudiante[0].documento || '—' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Nombre y apellidos</small>
                                <strong>{{ estudiante[0].name || '—' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Año lectivo</label>
                            <select v-model="form.ano" class="form-select" :disabled="modoEdicion">
                                <option value="">Seleccione</option>
                                <option v-for="item in date" :key="item" :value="item">{{ item }}</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Periodo</label>
                            <select v-model="form.periodo" class="form-select" :disabled="modoEdicion">
                                <option value="">Seleccione</option>
                                <option value="1">Primer periodo anual</option>
                                <option value="2">Segundo periodo anual</option>
                            </select>
                        </div>

                        <div class="col-md-3">
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

                        <div class="col-md-3">
                            <label class="form-label">Sede</label>
                            <select v-model="form.sede" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="Riohacha">Riohacha</option>
                                <option value="Fonseca">Fonseca</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor total matrícula</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" placeholder="0" :value="precioFormateado" @input="actualizarPrecio" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor seguro estudiantil <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" placeholder="0" :value="seguroFormateado" @input="actualizarSeguro" :disabled="modoEdicion" />
                            </div>
                            <small class="text-muted">Se crea automáticamente como pendiente al guardar.</small>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <button v-if="modoEdicion" @click.prevent="cancelarEdicion" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-times me-1"></i> Cancelar
                        </button>
                        <button v-if="!modoEdicion" @click.prevent="guardar" class="btn btn-success btn-sm">
                            <i class="fa fa-floppy-o me-1"></i> Guardar matrícula
                        </button>
                        <button v-if="modoEdicion" @click.prevent="actualizar" class="btn btn-primary btn-sm">
                            <i class="fa fa-refresh me-1"></i> Actualizar matrícula
                        </button>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Matriculas Anteriores</strong>
                        <div class="small text-muted">Historial académico y saldo de matrícula.</div>
                    </div>
                    <span class="text-muted small">Total: {{ pagination.total }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sede</th>
                                <th>Año</th>
                                <th>Semestre</th>
                                <th>Periodo</th>
                                <th>Saldo matrícula</th>
                                <th>Saldo pendiente</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in matriculas.data" :key="item.id">
                                <td>{{ item.sede || '—' }}</td>
                                <td>{{ item.año || '—' }}</td>
                                <td>{{ nombreSemestre(item.semestre) }}</td>
                                <td>{{ item.periodo || '—' }}</td>
                                <td class="fw-semibold">{{ formatoCOP(item.total) }}</td>
                                <td class="fw-semibold">{{ formatoCOP(item.valor) }}</td>
                                <td class="text-center">
                                    <button v-if="esAdmin" @click="editar(item)" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <span v-else class="text-muted">—</span>
                                </td>
                            </tr>
                            <tr v-if="!matriculas.data || matriculas.data.length === 0">
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-graduation-cap fa-2x mb-2 d-block"></i>
                                    No hay matrículas registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white" v-if="pagination.last_page > 1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="text-muted small">
                            Página {{ pagination.current_page }} de {{ pagination.last_page }} · Mostrando {{ pagination.from }}-{{ pagination.to }} de {{ pagination.total }}
                        </div>
                        <nav aria-label="Paginación de matrículas">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                    <button class="page-link" :disabled="pagination.current_page === 1" @click="cambiarPagina(pagination.current_page - 1)">Anterior</button>
                                </li>
                                <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                                    <button class="page-link" @click="cambiarPagina(page)">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                    <button class="page-link" :disabled="pagination.current_page === pagination.last_page" @click="cambiarPagina(pagination.current_page + 1)">Siguiente</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <loading v-model:active="isLoading" :is-full-page="true" />
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

<style scoped>
.matricula-page {
    padding: 6px 0 30px;
}

.matricula-header h4 {
    font-weight: 600;
    color: #344054;
}

.matricula-header p {
    font-size: 13px;
}

.matricula-page .card {
    border-radius: 10px;
}

.matricula-stat {
    transition: transform .2s ease, box-shadow .2s ease;
}

.matricula-stat:hover {
    transform: translateY(-2px);
}

.matricula-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
}

.matricula-page .card-header {
    padding: 18px 20px;
}

.matricula-page .card-body {
    padding: 20px;
}

.matricula-page .form-label {
    font-size: 12px;
    font-weight: 600;
    color: #667085;
    text-transform: uppercase;
    letter-spacing: .02em;
}

.matricula-page .form-control,
.matricula-page .form-select,
.matricula-page .input-group-text {
    min-height: 38px;
    border-color: #d0d5dd;
    font-size: 13px;
}

.matricula-page .input-group-text {
    background: #f8f9fa;
    color: #667085;
}

.matricula-student-box {
    padding: 15px 18px;
    border: 1px solid #e4e7ec;
    border-radius: 8px;
    background: #f8fafc;
}

.matricula-student-box small {
    font-size: 11px;
}

.matricula-page .table thead th {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #667085;
    white-space: nowrap;
    padding: 13px 15px;
    border-bottom: 1px solid #e4e7ec;
}

.matricula-page .table tbody td {
    font-size: 13px;
    padding: 13px 15px;
    color: #475467;
}

.matricula-page .table tbody tr:hover {
    background: #f8fafc;
}

.matricula-page .btn {
    border-radius: 6px;
}

.matricula-page .pagination .page-link {
    border-radius: 5px;
    margin: 0 2px;
}

@media (max-width: 768px) {
    .matricula-header {
        gap: 15px;
    }

    .matricula-header .btn {
        width: 100%;
    }
}
</style>