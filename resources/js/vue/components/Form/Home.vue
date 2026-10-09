<template>
    <main class="main" id="main">
        <div class="container-fluid">
            <!-- GRÁFICA DE MATRICULADOS: visible para todos los usuarios autenticados -->
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body">
                            <h5 class="text-lg font-semibold mb-4 text-center border rounded p-2 bg-light">
                                Alumnos matriculados
                            </h5>

                            <hr>

                            <apexchart
                                width="100%"
                                height="250"
                                type="bar"
                                :options="options"
                                :series="series"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESUMEN DE PAGOS: únicamente para administradores -->
            <template v-if="esAdmin">
                <div class="row mt-3">
                    <div class="col-12 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <label for="semestre" class="fw-bold mb-0">Semestre:</label>
                                    <select id="semestre" v-model="semestreSeleccionado" @change="obtenerResumenPagos" class="form-select" style="width: 240px;" :disabled="cargandoSemestres">
                                        <option :value="null" disabled>Seleccione un semestre</option>
                                        <option v-for="semestre in semestres" :key="semestre.id" :value="semestre.periodo">
                                            {{ semestre.nombre }}<template v-if="semestre.anio"> - {{ semestre.anio }}</template>
                                        </option>
                                    </select>
                                    <span v-if="cargandoResumen" class="text-muted small">Cargando resumen...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="text-muted">TOTAL DEUDA</h6>
                                <h3 class="fw-bold">$ {{ formatoMoneda(resumen.total_deuda) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="text-muted">TOTAL PAGADO</h6>
                                <h3 class="fw-bold">$ {{ formatoMoneda(resumen.total_pagado) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="text-muted">TOTAL PENDIENTE</h6>
                                <h3 class="fw-bold">$ {{ formatoMoneda(resumen.total_pendiente) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOG DEL APLICATIVO: únicamente para administradores -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <h5 class="text-lg font-semibold mb-4 text-center border rounded p-2 bg-light">Log del aplicativo</h5>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Responsable</th>
                                                <th>Fecha</th>
                                                <th>Recaudo Eliminado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="respaldos.length === 0">
                                                <td colspan="3" class="text-center text-muted">No hay registros disponibles.</td>
                                            </tr>
                                            <tr v-for="respaldo in respaldos" :key="respaldo.id">
                                                <td>{{ respaldo.user?.name || 'Sin responsable' }}</td>
                                                <td>{{ respaldo.fecha || respaldo.created_at || '-' }}</td>
                                                <td>$ {{ formatoMoneda(respaldo.valores) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div v-if="pagination.last_page > 1" class="col-12">
                                    <div class="demo-inline-spacing">
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination pagination-sm">
                                                <li class="page-item prev" v-if="pagination.current_page > 1">
                                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                                        <i class="tf-icon bx bx-chevrons-left"></i>
                                                    </a>
                                                </li>
                                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="{ active: page === isActived }">
                                                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                                </li>
                                                <li class="page-item next" v-if="pagination.current_page < pagination.last_page">
                                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
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
            </template>
        </div>
    </main>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts'
import axios from 'axios'

export default {
    components: { apexchart: VueApexCharts },

    data() {
        const adminMeta = document.querySelector('meta[name="is-admin"]')

        return {
            esAdmin: adminMeta?.getAttribute('content') === '1',
            respaldos: [],
            semestres: [],
            semestreSeleccionado: null,
            cargandoSemestres: false,
            cargandoResumen: false,
            resumen: { total_deuda: 0, total_pagado: 0, total_pendiente: 0 },
            pagination: { total: 0, current_page: 1, per_page: 5, last_page: 1, from: 0, to: 0 },
            offset: 2,
            options: {
                chart: { id: 'matriculados-mes', toolbar: { show: false } },
                xaxis: { categories: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
                dataLabels: { enabled: true },
                plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                yaxis: { min: 0, forceNiceScale: true, title: { text: 'Cantidad de estudiantes' } },
                tooltip: { y: { formatter(value) { return value + ' estudiantes' } } },
            },
            series: [{ name: 'Alumnos matriculados', data: [0,0,0,0,0,0,0,0,0,0,0,0] }],
        }
    },

    computed: {
        isActived() { return this.pagination.current_page },
        pagesNumber() {
            if (!this.pagination.last_page || this.pagination.last_page <= 1) return []
            let from = Math.max(1, this.pagination.current_page - this.offset)
            let to = Math.min(this.pagination.last_page, from + (this.offset * 2))
            const pagesArray = []
            while (from <= to) { pagesArray.push(from); from++ }
            return pagesArray
        },
    },

    mounted() {
        // Todos los usuarios autenticados pueden consultar la gráfica.
        this.obtenerMatriculados()

        // Solo Admin consulta datos financieros y el log.
        if (this.esAdmin) {
            this.obtenerRespaldos(1)
            this.obtenerSemestres()
        }
    },

    methods: {
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page || page === this.pagination.current_page) return
            this.obtenerRespaldos(page)
        },

        async obtenerMatriculados() {
            try {
                const res = await axios.get('/dashboard/matriculados-mes')
                const datos = Array.isArray(res.data) ? res.data : []
                this.options.xaxis.categories = datos.map(item => item.mes)
                this.series[0].data = datos.map(item => Number(item.cantidad || 0))
            } catch (error) {
                console.error('Error obteniendo matriculados:', error)
                this.options.xaxis.categories = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
                this.series[0].data = [0,0,0,0,0,0,0,0,0,0,0,0]
            }
        },

        async obtenerRespaldos(page = 1) {
            if (!this.esAdmin) return
            try {
                const res = await axios.get('/respaldos', { params: { page } })
                this.respaldos = Array.isArray(res.data.respaldos) ? res.data.respaldos : []
                this.pagination = { ...this.pagination, ...(res.data.pagination || {}) }
            } catch (error) {
                console.error('Error obteniendo log del aplicativo:', error)
                this.respaldos = []
            }
        },

        formatoMoneda(valor) {
            return Number(valor || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },

        async obtenerResumenPagos() {
            if (!this.esAdmin || this.semestreSeleccionado === null || this.semestreSeleccionado === '') return
            this.cargandoResumen = true
            try {
                const res = await axios.get('/dashboard/resumen-pagos', { params: { semestre: this.semestreSeleccionado } })
                this.resumen = { ...this.resumen, ...(res.data || {}) }
            } catch (error) {
                console.error('Error obteniendo resumen de pagos:', error)
                this.resumen = { total_deuda: 0, total_pagado: 0, total_pendiente: 0 }
            } finally {
                this.cargandoResumen = false
            }
        },

        async obtenerSemestres() {
            if (!this.esAdmin) return
            this.cargandoSemestres = true
            try {
                const res = await axios.get('/dashboard/semestres')
                this.semestres = Array.isArray(res.data) ? res.data : []
                if (this.semestres.length > 0) {
                    this.semestreSeleccionado = this.semestres[0].periodo
                    await this.obtenerResumenPagos()
                } else {
                    this.semestreSeleccionado = null
                    this.resumen = { total_deuda: 0, total_pagado: 0, total_pendiente: 0 }
                }
            } catch (error) {
                console.error('Error obteniendo semestres:', error)
                this.semestres = []
                this.semestreSeleccionado = null
                this.resumen = { total_deuda: 0, total_pagado: 0, total_pendiente: 0 }
            } finally {
                this.cargandoSemestres = false
            }
        },
    },
}
</script>
