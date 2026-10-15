<template>
    <main class="main notas-page" id="main">
        <div class="container-fluid py-3">

            <!-- LISTADO DE MATRICULADOS -->
            <section class="notas-card" id="listado" v-show="vista === 'listado'">
                <div class="section-title">Matriculados</div>

                <div class="table-responsive">
                    <table class="table notas-table mb-0">
                        <thead>
                            <tr>
                                <th>DOCUMENTO</th>
                                <th>NOMBRE</th>
                                <th>SEMESTRE</th>
                                <th>ACCIONES</th>
                            </tr>
                            <tr class="filter-row">
                                <th>
                                    <input type="search" id="searchbox" class="form-control form-control-sm"
                                        v-model="identidad" @input="buscar" placeholder="Documento">
                                </th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="items in nota" :key="items.id">
                                <td>{{ items.documento }}</td>
                                <td class="text-uppercase">{{ items.name }}</td>
                                <td>{{ items.semestre }}</td>
                                <td class="actions-cell">
                                    <button @click="consulta(items)" class="btn btn-secondary btn-sm action-btn"
                                        title="Imprimir">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <button v-if="!tieneMaterias(items)" @click="materia(items)"
                                        class="btn btn-info btn-sm action-btn" title="Agregar Materia">
                                        <i class="fa fa-file-text" aria-hidden="true"></i>
                                    </button>
                                    <button @click="getNotas(items)" class="btn btn-success btn-sm action-btn"
                                        title="Agregar Notas">
                                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!nota.length">
                                <td colspan="4" class="empty-row">No se encontraron estudiantes matriculados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="total-label">Total: <strong>{{ pagination.total || 0 }}</strong></div>
                    <nav v-if="pagesNumber.length" aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page - 1)">«</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="{ active: page === isActived }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item"
                                :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page + 1)">»</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>

            <!-- GESTION DE NOTAS -->
            <section class="notas-card gestion-card" id="dos" v-show="vista === 'gestion'">
                <div class="section-title">Gestión de Notas</div>

                <form @submit.prevent="guardarNota">
                    <div class="form-panel">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="field-label">MATERIA</label>
                                <input type="text" v-model="form.materia" class="form-control" disabled>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="field-label">PRIMER CORTE</label>
                                <input type="number" step="0.1" min="0" max="5" v-model="form.corte1"
                                    class="form-control">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="field-label">SEGUNDO CORTE</label>
                                <input type="number" step="0.1" min="0" max="5" v-model="form.corte2"
                                    class="form-control">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="field-label">TERCER CORTE</label>
                                <input type="number" step="0.1" min="0" max="5" v-model="form.corte3"
                                    class="form-control">
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button type="submit" v-if="editando" class="btn btn-outline-success w-100">
                                    <i class="fa fa-refresh me-1"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-4">
                    <table class="table notas-table mb-0">
                        <thead>
                            <tr>
                                <th>SEMESTRE</th>
                                <th>MATERIA</th>
                                <th>CORTE 1</th>
                                <th>CORTE 2</th>
                                <th>CORTE 3</th>
                                <th>ACUMULADO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="nota in notas" :key="nota.id">
                                <td>{{ nota.semestre }}</td>
                                <td class="text-uppercase">{{ nota.materia }}</td>
                                <td>{{ nota.corte1 ?? '-' }}</td>
                                <td>{{ nota.corte2 ?? '-' }}</td>
                                <td>{{ nota.corte3 ?? '-' }}</td>
                                <td>{{ calcularAcumulado(nota) }}</td>
                                <td class="actions-cell">
                                    <button @click="editarNota(nota)" class="btn btn-warning btn-sm action-btn"
                                        title="Editar">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!notas.length">
                                <td colspan="7" class="empty-row">Seleccione un estudiante para consultar sus notas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button v-if="editando" type="button" @click="cancelarEdicion"
                        class="btn btn-outline-secondary">
                        <i class="bx bx-window-close me-1"></i> Cancelar
                    </button>
                    <button @click="ocultar" type="button" class="btn btn-warning">
                        <i class="bx bx-log-out me-1"></i> Salir
                    </button>
                </div>
            </section>

            <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                :is-full-page="true" />
        </div>
    </main>
</template>

<script>
import axios from 'axios'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';

export default {
    components: {
        Loading,
        'Pagination': LaravelVuePagination,
    },
    computed: {
        isActived() {
            return this.pagination.current_page;
        },
        pagesNumber() {
            if (!this.pagination.to) {
                return [];
            }
            let from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            let to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            const pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    data() {
        return {
            salir: false,
            isLoading: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            onCancel: this.onCancel,
            identidad: '',
            vista: 'listado',
            asignaturaActual: null,
            pagination: {
                total: 0,
                current_page: 0,
                per_page: 0,
                last_page: 0,
                from: 0,
                to: 0,
            },
            offset: 2,
            nota: [],
            notas: [],
            form: {
                semestre: '',
                materia: '',
                materia_id: null,
                asignatura_id: null,
                corte1: null,
                corte2: null,
                corte3: null,
                habilitada_recuperacion: false
            },
            editando: false,
            idEdit: null,
            matriculas: [],
        }
    },
    mounted() {
        this.created(1);
    },
    methods: {
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.pagination.current_page = page;
            this.created(page);
        },
        tieneMaterias(items) {
            return String(items?.mat_matr ?? '').trim().toLowerCase() === 'si';
        },
        calcularAcumulado(nota) {
            const c1 = Number(nota.corte1);
            const c2 = Number(nota.corte2);
            const c3 = Number(nota.corte3);
            if ([nota.corte1, nota.corte2, nota.corte3].some(v => v === null || v === undefined || v === '')) {
                return '-';
            }
            return (c1 * 0.35 + c2 * 0.30 + c3 * 0.35).toFixed(2);
        },
        imprimirTabla() {
            window.print();
        },
        async consulta(items) {
            try {
                const res = await axios.get(`/notas/${items.asignatura_id}`);
                this.notas = Array.isArray(res.data) ? res.data : [];
                this.imprimir(this.notas);
            } catch (error) {
                console.error(error);
                if (window.toastr) toastr.error('No fue posible consultar las notas.');
            }
        },
        imprimir(notas) {
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            const primera = Array.isArray(notas) ? notas[0] : notas;
            const semestres = { 1: 'Primer', 2: 'Segundo', 3: 'Tercer', 4: 'Cuarto', 5: 'Quinto' };
            const sem = semestres[primera?.semestre] || 'Quinto';
            const contenido = `
<html><head><title>Calificaciones</title><style>
body { font-family: Arial; padding: 20px; font-size: 8px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8px; }
th,td { border: 1px solid #999; padding: 5px; }
#ti { text-align: center; } #im { text-align: right; }
</style></head><body>
<div><img src="${logoUrl}" alt="Logo" style="max-height:60px;"><p id="ti">Fundacion para el Desarrollo Educativo y Social de la Guajira</p><p id="ti"><strong>NIT:</strong> 901691754-7</p>
<h2>Reporte de Calificaciones</h2><p id="im"><strong>Fecha:</strong> ${new Date().toLocaleDateString('es-CO')}</p>
<p><strong>Estudiante:</strong> ${primera?.name || ''}</p><p><strong>Documento:</strong> ${primera?.documento || ''}</p><p><strong>Semestre:</strong> ${sem}</p></div>
<table><thead><tr><th>Materia</th><th>Primer corte</th><th>Segundo corte</th><th>Tercer corte</th></tr></thead><tbody>
${(Array.isArray(notas) ? notas : []).map(n => `<tr><td>${n.materia || ''}</td><td>${n.corte1 ?? '-'}</td><td>${n.corte2 ?? '-'}</td><td>${n.corte3 ?? '-'}</td></tr>`).join('')}
</tbody></table></body></html>`;
            const ventana = window.open('', '_blank');
            if (!ventana) return;
            ventana.document.write(contenido);
            ventana.document.close();
            ventana.onload = () => {
                setTimeout(() => {
                    ventana.focus();
                    ventana.print();
                    ventana.close();
                }, 300);
            };
        },
        buscar() {
            this.isLoading = true;
            axios.post('/notas/buscar', { identidad: this.identidad })
                .then((res) => {
                    this.nota = res.data.nota.data;
                    this.pagination = res.data.pagination;
                })
                .catch((error) => {
                    console.error(error);
                    this.nota = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        async created(page = 1) {
            this.vista = 'listado';
            this.isLoading = true;
            try {
                const res = await axios.get('/nota?page=' + page);
                this.nota = res.data.nota.data;
                this.pagination = res.data.pagination;
            } catch (error) {
                console.error('No fue posible cargar los matriculados:', error);
                this.nota = [];
                if (window.toastr) toastr.error('No fue posible cargar los matriculados.');
            } finally {
                this.isLoading = false;
            }
        },
        ocultar() {
            this.vista = 'listado';
            this.editando = false;
            this.idEdit = null;
            this.created(this.pagination.current_page || 1);
        },
        async materia(items) {
            this.isLoading = true;
            try {
                const response = await axios.post('/agregar/materia', {
                    semestre: items.semestre,
                    matricula: items.asignatura_id,
                });
                items.mat_matr = 'Si';
                if (window.toastr) toastr.success(response.data?.message || 'Materias agregadas correctamente.');
                await this.getNotas(items);
            } catch (error) {
                console.error('No fue posible agregar las materias:', error);
                const mensaje = error.response?.data?.message || 'No fue posible agregar las materias del semestre.';
                if (window.toastr) toastr.error(mensaje);
                else window.alert(mensaje);
            } finally {
                this.isLoading = false;
            }
        },
        async getNotas(items) {
            if (!items?.asignatura_id) return;
            this.salir = false;
            this.idEdit = null;
            this.editando = false;
            this.asignaturaActual = items.asignatura_id;
            this.vista = 'gestion';
            try {
                const res = await axios.get(`/notas/${items.asignatura_id}`);
                this.notas = Array.isArray(res.data) ? res.data : [];
                this.form = {
                    semestre: items.semestre,
                    materia: '',
                    materia_id: null,
                    asignatura_id: items.asignatura_id,
                    corte1: null,
                    corte2: null,
                    corte3: null,
                    habilitada_recuperacion: false
                };
            } catch (error) {
                console.error('No fue posible consultar las notas:', error);
                this.notas = [];
                if (window.toastr) toastr.error(error.response?.data?.message || 'No fue posible cargar las notas del estudiante.');
            }
        },
        async guardarNota() {
            if (!this.idEdit) return;
            this.isLoading = true;
            try {
                const res = await axios.put(`/nota/${this.idEdit}`, this.form);
                const asignaturaId = res.data?.asignatura_id || this.asignaturaActual;
                if (window.toastr) toastr.success('Nota actualizada correctamente.');
                await this.getNotas({ asignatura_id: asignaturaId, semestre: this.form.semestre });
            } catch (error) {
                console.error(error);
                if (window.toastr) toastr.error(error.response?.data?.message || 'No fue posible actualizar la nota.');
            } finally {
                this.isLoading = false;
            }
        },
        editarNota(nota) {
            this.form = { ...nota };
            this.idEdit = nota.id;
            this.editando = true;
            this.salir = false;
        },
        cancelarEdicion() {
            this.resetForm();
        },
        resetForm() {
            this.form = {
                semestre: '',
                materia: '',
                materia_id: null,
                asignatura_id: this.asignaturaActual,
                corte1: null,
                corte2: null,
                corte3: null,
                habilitada_recuperacion: false
            };
            this.editando = false;
            this.idEdit = null;
        },
        async eliminarNota(id) {
            if (confirm('¿Eliminar esta nota?')) {
                await axios.delete(`/notas/${id}`);
                if (this.asignaturaActual) {
                    await this.getNotas({ asignatura_id: this.asignaturaActual, semestre: this.form.semestre });
                }
            }
        }
    },
}
</script>

<style scoped>
.notas-page {
    background: #f4f6f8;
    min-height: 100%;
}
.notas-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(40, 50, 60, 0.10);
    padding: 18px 20px;
    margin-bottom: 20px;
    border: 1px solid #edf0f3;
}
.section-title {
    border: 1px solid #d7dde5;
    background: #fff;
    color: #536b86;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    padding: 6px 10px;
    margin-bottom: 16px;
    text-transform: uppercase;
}
.notas-table {
    width: 100%;
    border-collapse: collapse;
    color: #536b86;
}
.notas-table th {
    background: #fff;
    color: #536b86;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .5px;
    text-align: center;
    vertical-align: middle;
    padding: 11px 8px;
    border: 1px solid #d7dde5;
    white-space: nowrap;
}
.notas-table td {
    color: #536b86;
    font-size: 13px;
    text-align: center;
    vertical-align: middle;
    padding: 10px 8px;
    border: 1px solid #d7dde5;
}
.notas-table tbody tr:hover { background: #fafbfc; }
.filter-row th { padding: 8px 20px; }
.filter-row input { min-height: 28px; border: 1px solid #d7dde5; box-shadow: none; }
.actions-cell { white-space: nowrap; }
.action-btn {
    width: 36px; height: 30px; padding: 0; margin: 0 2px; border-radius: 5px;
    display: inline-flex; align-items: center; justify-content: center;
}
.table-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 14px; }
.total-label { color: #65758b; font-size: 13px; }
.pagination .page-link { color: #65758b; border-color: #e1e6eb; min-width: 30px; text-align: center; }
.pagination .page-item.active .page-link { background: #686cff; border-color: #686cff; color: #fff; }
.form-panel { border: 1px solid #d7dde5; padding: 16px 10px; }
.field-label { display: block; color: #536b86; font-size: 12px; font-weight: 500; margin-bottom: 6px; text-transform: uppercase; }
.form-panel .form-control { border-color: #d7dde5; min-height: 38px; color: #536b86; box-shadow: none; }
.form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; padding-top: 14px; border-top: 1px solid #d7dde5; }
.empty-row { color: #8a98a9 !important; padding: 18px !important; }
@media (max-width: 768px) {
    .notas-card { padding: 12px; }
    .table-footer { flex-direction: column; align-items: flex-start; gap: 10px; }
    .notas-table th, .notas-table td { font-size: 11px; }
}
</style>