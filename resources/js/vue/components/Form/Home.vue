<template>

    <main class="main" id="main">

        <div class="container-fluid">

            <!-- GRÁFICA -->
            <div class="row">

                <div class="col-12 col-md-12">

                    <div class="card border">

                        <div class="card-body">

                            <h5 class="text-lg font-semibold mb-4 text-center border rounded p-2 bg-light">
                                Alumnos matriculados
                            </h5>

                            <hr>

                            <apexchart width="100%" height="250" type="bar" :options="options" :series="series" />

                        </div>

                    </div>

                </div>

            </div>


           

            <!-- GRAFICA DE PAGOS VS DEUDAS -->
            <div class="row mt-3">

                <!-- SELECTOR SEMESTRE -->
               <!--  <div class="col-12 mb-3  ">
                    <div class="d-flex align-items-center">
                        <label class="fw-bold me-2">
                            Semestre:
                        </label>
                        <select v-model="semestreSeleccionado" @change="obtenerResumenPagos" class="form-select"
                            style="width: 180px;">
                            <option v-for="semestre in semestres" :key="semestre.id" :value="semestre.nombre">
                                {{ semestre.nombre }}
                            </option>
                        </select>
                    </div>
                </div> -->


                <!-- TOTAL DEUDA -->
                <div class="col-md-4 mb-3">
                    <div class="card border">
                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                TOTAL DEUDA
                            </h6>

                            <h3 class="fw-bold">
                                $ {{ formatoMoneda(resumen.total_deuda) }}
                            </h3>

                        </div>
                    </div>
                </div>


                <!-- TOTAL PAGADO -->
                <div class="col-md-4 mb-3">
                    <div class="card border">
                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                TOTAL PAGADO
                            </h6>

                            <h3 class="fw-bold">
                                $ {{ formatoMoneda(resumen.total_pagado) }}
                            </h3>

                        </div>
                    </div>
                </div>


                <!-- TOTAL PENDIENTE -->
                <div class="col-md-4 mb-3">
                    <div class="card border">
                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                TOTAL PENDIENTE
                            </h6>

                            <h3 class="fw-bold">
                                $ {{ formatoMoneda(resumen.total_pendiente) }}
                            </h3>

                        </div>
                    </div>
                </div>

            </div>
             <!-- LOG DEL APLICATIVO -->
             <div class="row mt-2">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">

                                <h5 class="text-lg font-semibold mb-4 text-center border rounded p-2 bg-light">
                                    Log del aplicativo
                                </h5>
                                <table class="table table-bordered table-hover">

                                    <thead>
                                        <tr>
                                            <th>Responsable</th>
                                            <th>Fecha</th>
                                            <th>Recaudo Eliminado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="respaldo in respaldos" :key="respaldo.id">
                                            <td>
                                                {{ respaldo.user?.name }}
                                            </td>
                                            <td>
                                                {{ respaldo.fecha }}
                                            </td>
                                            <td>
                                                $ {{ formatoMoneda(respaldo.valores) }}
                                            </td>
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
                </div>
            </div>
        </div>

    </main>

</template>

<script>
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
import VueApexCharts from 'vue3-apexcharts'
import axios from 'axios'

export default {

    components: {
        apexchart: VueApexCharts,
        'Pagination': LaravelVuePagination,
    },

    data() {

        return {
            fullPage: '',
            respaldos: [],
            semestres: [],
            semestreSeleccionado: null,
            resumen: {
                total_deuda: 0,
                total_pagado: 0,
                total_pendiente: 0,
                estudiantes: 0,
                estudiantes_pagaron: 0,
                estudiantes_pendientes: 0
            },
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
            options: {

                chart: {
                    id: 'matriculados-mes',
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {

                    categories: [
                        'Enero',
                        'Febrero',
                        'Marzo',
                        'Abril',
                        'Mayo',
                        'Junio',
                        'Julio',
                        'Agosto',
                        'Septiembre',
                        'Octubre',
                        'Noviembre',
                        'Diciembre'
                    ]
                },

                dataLabels: {
                    enabled: true
                },
                plotOptions: {

                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    title: {
                        text: 'Cantidad de estudiantes'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + ' estudiantes'
                        }

                    }

                }

            },

            series: [

                {
                    name: 'Alumnos matriculados',

                    data: [
                        0, 0, 0, 0, 0, 0,
                        0, 0, 0, 0, 0, 0
                    ]
                }

            ]

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

        this.obtenerMatriculados()
        this.obtenerRespaldos()
        this.obtenerSemestres()
        this.obtenerResumenPagos()
    },

    methods: {
        changePage: function (page) {
            this.pagination.current_page = page;
            this.obtenerRespaldos(page);
        },
        async obtenerMatriculados() {
            axios.get('/dashboard/matriculados-mes')
                .then(async res => {

                    const datos = res.data

                    this.options.xaxis.categories = datos.map(
                        item => item.mes
                    )
                    this.series[0].data = datos.map(
                        item => item.cantidad
                    )

                }).catch(error => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        async obtenerRespaldos(page) {

            try {

                const res = await axios.get(
                    '/respaldos?page=' + page
                )
                this.respaldos = res.data.respaldos
                this.pagination = res.data.pagination
            } catch (error) {
                console.error(error)
                this.respaldos = []

            }

        },
        formatoMoneda(valor) {

            return Number(valor || 0).toLocaleString(
                'es-CO'
            )

        }
        ,
        async obtenerResumenPagos() {
            try {
                const res = await axios.get(
                    '/dashboard/resumen-pagos',
                    {
                        params: {
                            semestre: this.semestreSeleccionado
                        }
                    }
                )
                this.resumen = res.data
            } catch (error) {
                console.error(
                    'Error obteniendo resumen de pagos:',
                    error
                )

            }

        },
        async obtenerSemestres() {
            try {
                const res = await axios.get('/dashboard/semestres')
                this.semestres = res.data
                if (this.semestres.length > 0) {
                    this.semestreSeleccionado = this.semestres[0].nombre
                    this.obtenerResumenPagos()
                }

            } catch (error) {

                console.error('Error obteniendo semestres:', error)

            }

        },


    }

}



</script>