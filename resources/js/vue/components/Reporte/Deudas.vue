<template>
    <main class="main" id="main">
        <div class="container-fluid py-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Informe de Deudas</h5>
                            <small class="text-muted">Consulta la cartera pendiente por año y período.</small>
                        </div>
                        <button class="btn btn-success" type="button" @click="descargarExcel" :disabled="isLoading || descargando">
                            <i class="fa fa-file-excel-o me-1"></i>
                            {{ descargando ? 'Generando...' : 'Descargar XLSX' }}
                        </button>
                    </div>

                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Año</label>
                                <select v-model="filtros.ano" class="form-select">
                                    <option value="">Todos</option>
                                    <option v-for="ano in anos" :key="ano" :value="ano">{{ ano }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Período</label>
                                <select v-model="filtros.periodo" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Documento o estudiante</label>
                                <input v-model.trim="filtros.buscar" type="text" class="form-control" placeholder="Buscar..." @keyup.enter="consultar(1)">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-4 pt-2">
                                    <input id="soloDeuda" v-model="filtros.solo_deuda" class="form-check-input" type="checkbox">
                                    <label class="form-check-label" for="soloDeuda">Solo con deuda</label>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex gap-2">
                                <button class="btn btn-primary" type="button" title="Buscar" @click="consultar(1)">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button class="btn btn-light border" type="button" title="Limpiar" @click="limpiar">
                                    <i class="fa fa-eraser"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="summary-card">
                                <span>Estudiantes</span>
                                <strong>{{ resumen.estudiantes }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <span>Total matriculado</span>
                                <strong>{{ formatoCOP(resumen.matriculado) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card danger">
                                <span>Total deuda</span>
                                <strong>{{ formatoCOP(resumen.deuda) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div v-if="error" class="alert alert-danger">{{ error }}</div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Estudiante</th>
                                    <th>Año</th>
                                    <th>Período</th>
                                    <th>Semestre</th>
                                    <th class="text-end">Valor matrícula</th>
                                    <th class="text-end">Pagado</th>
                                    <th class="text-end">Deuda</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in deudas" :key="item.clave">
                                    <td>{{ item.documento }}</td>
                                    <td>{{ item.estudiante }}</td>
                                    <td>{{ item.ano }}</td>
                                    <td>{{ item.periodo }}</td>
                                    <td>{{ item.semestre_nombre }}</td>
                                    <td class="text-end">{{ formatoCOP(item.matriculado) }}</td>
                                    <td class="text-end">{{ formatoCOP(item.pagado) }}</td>
                                    <td class="text-end fw-bold text-danger">{{ formatoCOP(item.deuda) }}</td>
                                    <td>
                                        <span v-if="Number(item.deuda) > 0" class="badge bg-label-warning">Pendiente</span>
                                        <span v-else class="badge bg-label-success">Pagado</span>
                                    </td>
                                </tr>
                                <tr v-if="!isLoading && deudas.length === 0">
                                    <td colspan="9" class="text-center py-4 text-muted">No hay información para los filtros seleccionados.</td>
                                </tr>
                            </tbody>
                            <tfoot v-if="deudas.length">
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">Totales de la página</td>
                                    <td class="text-end">{{ formatoCOP(totalPagina.matriculado) }}</td>
                                    <td class="text-end">{{ formatoCOP(totalPagina.pagado) }}</td>
                                    <td class="text-end text-danger">{{ formatoCOP(totalPagina.deuda) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3" v-if="pagination.last_page > 1">
                        <small class="text-muted">Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total }}</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                    <button class="page-link" type="button" @click="consultar(pagination.current_page - 1)" :disabled="pagination.current_page <= 1">Anterior</button>
                                </li>
                                <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                                    <button class="page-link" type="button" @click="consultar(page)">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                    <button class="page-link" type="button" @click="consultar(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page">Siguiente</button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div v-if="isLoading" class="text-center py-4 text-muted">
                        <i class="fa fa-spinner fa-spin me-1"></i> Consultando información...
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            deudas: [],
            anos: [],
            filtros: {
                ano: '',
                periodo: '',
                buscar: '',
                solo_deuda: true,
            },
            resumen: {
                estudiantes: 0,
                matriculado: 0,
                deuda: 0,
            },
            pagination: {
                total: 0,
                current_page: 1,
                per_page: 15,
                last_page: 1,
                from: 0,
                to: 0,
            },
            isLoading: false,
            descargando: false,
            error: '',
        };
    },
    computed: {
        paginas() {
            const total = Number(this.pagination.last_page || 1);
            const actual = Number(this.pagination.current_page || 1);
            const inicio = Math.max(1, actual - 2);
            const fin = Math.min(total, inicio + 4);
            const paginas = [];
            for (let i = inicio; i <= fin; i++) paginas.push(i);
            return paginas;
        },
        totalPagina() {
            return this.deudas.reduce((totales, item) => {
                totales.matriculado += Number(item.matriculado) || 0;
                totales.pagado += Number(item.pagado) || 0;
                totales.deuda += Number(item.deuda) || 0;
                return totales;
            }, { matriculado: 0, pagado: 0, deuda: 0 });
        },
    },
    mounted() {
        this.cargarAnios();
        this.consultar(1);
    },
    methods: {
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(Number(valor) || 0);
        },
        async cargarAnios() {
            try {
                const response = await axios.get('/reporte/deudas/anos');
                this.anos = response.data.anos || [];
            } catch (e) {
                this.anos = [];
            }
        },
        async consultar(page = 1) {
            if (page < 1) return;
            this.isLoading = true;
            this.error = '';
            try {
                const response = await axios.get('/reporte/deudas', {
                    params: { ...this.filtros, page },
                });
                this.deudas = response.data.deudas?.data || [];
                this.pagination = response.data.pagination || this.pagination;
                this.resumen = response.data.resumen || this.resumen;
            } catch (e) {
                this.deudas = [];
                this.error = e?.response?.data?.message || 'No fue posible consultar el informe de deudas.';
            } finally {
                this.isLoading = false;
            }
        },
        limpiar() {
            this.filtros = { ano: '', periodo: '', buscar: '', solo_deuda: true };
            this.consultar(1);
        },
        async descargarExcel() {
            this.descargando = true;
            try {
                const response = await axios.get('/reporte/deudas/xlsx', {
                    params: this.filtros,
                    responseType: 'blob',
                });
                const blob = new Blob([response.data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                });
                const url = window.URL.createObjectURL(blob);
                const enlace = document.createElement('a');
                enlace.href = url;
                enlace.download = `informe_deudas_${this.filtros.ano || 'todos'}_${this.filtros.periodo || 'todos'}.xlsx`;
                document.body.appendChild(enlace);
                enlace.click();
                enlace.remove();
                window.URL.revokeObjectURL(url);
                if (window.toastr) toastr.success('Archivo XLSX generado correctamente.');
            } catch (e) {
                if (window.toastr) toastr.error('No fue posible generar el archivo XLSX.');
            } finally {
                this.descargando = false;
            }
        },
    },
};
</script>

<style scoped>
.filter-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 18px;
}
.summary-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 16px;
    background: #fff;
}
.summary-card span {
    display: block;
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 5px;
}
.summary-card strong {
    font-size: 20px;
}
.summary-card.danger strong {
    color: #dc3545;
}
.table th {
    white-space: nowrap;
}
</style>
