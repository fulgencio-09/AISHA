<template>
    <div class="container-fluid reportes-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fa fa-file-excel-o me-2"></i>Reportes</h4>
                <p class="text-muted mb-0">Exporta los listados de pagos y estudiantes matriculados por rango de fechas.</p>
            </div>
            <button class="btn btn-outline-secondary" @click="limpiar">
                <i class="fa fa-eraser me-1"></i> Limpiar filtros
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 report-card">
                    <div class="card-header bg-white border-0 d-flex align-items-center">
                        <span class="report-icon bg-primary-subtle text-primary"><i class="fa fa-money"></i></span>
                        <div>
                            <h5 class="mb-1">Listado de pagos realizados</h5>
                            <small class="text-muted">Genera el archivo Excel de pagos por fecha.</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha inicial</label>
                                <input type="date" class="form-control" v-model="finicial">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha final</label>
                                <input type="date" class="form-control" v-model="ffinal">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" @click="agregar" :disabled="isLoading">
                            <i class="fa fa-download me-1"></i> Exportar pagos
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 report-card">
                    <div class="card-header bg-white border-0 d-flex align-items-center">
                        <span class="report-icon bg-success-subtle text-success"><i class="fa fa-users"></i></span>
                        <div>
                            <h5 class="mb-1">Listado de estudiantes matriculados</h5>
                            <small class="text-muted">Genera el archivo Excel de matrículas por fecha.</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha inicial</label>
                                <input type="date" class="form-control" v-model="finicial1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha final</label>
                                <input type="date" class="form-control" v-model="ffinal1">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-end">
                        <button type="button" class="btn btn-success" @click="exportar" :disabled="isLoading">
                            <i class="fa fa-download me-1"></i> Exportar matrículas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :is-full-page="true" />
    </div>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { utils, writeFileXLSX } from 'xlsx';
import exportFromJSON from 'export-from-json';

export default {
    name: 'ReportesRelacion',
    components: { Loading },
    data() {
        return {
            isLoading: false,
            finicial: '',
            ffinal: '',
            finicial1: '',
            ffinal1: '',
            reporte: [],
        };
    },
    methods: {
        agregar() {
            this.isLoading = true;
            axios.post('/exportar', {
                finicial: this.finicial,
                ffinal: this.ffinal,
            }).then((res) => {
                this.reporte = res.data.reporte || [];
                const wb = utils.book_new();
                const ws = utils.json_to_sheet(this.reporte);
                utils.book_append_sheet(wb, ws, 'BASE DE DATOS');
                writeFileXLSX(wb, 'Listado de cuotas por estudiantes.xlsx');
                toastr.success('Reporte de pagos exportado correctamente.');
            }).catch((error) => {
                toastr.error(error?.response?.data?.message || 'No fue posible exportar el reporte de pagos.');
            }).finally(() => {
                this.isLoading = false;
            });
        },
        exportar() {
            this.isLoading = true;
            axios.post('/exportar_', {
                finicial: this.finicial1,
                ffinal: this.ffinal1,
            }).then((res) => {
                this.reporte = res.data.reporte || [];
                exportFromJSON({
                    data: this.reporte,
                    fileName: 'Listado de estudiantes matriculados',
                    exportType: exportFromJSON.types.xls,
                });
                toastr.success('Reporte de estudiantes exportado correctamente.');
            }).catch((error) => {
                toastr.error(error?.response?.data?.message || 'No fue posible exportar el reporte de estudiantes.');
            }).finally(() => {
                this.isLoading = false;
            });
        },
        limpiar() {
            this.finicial = '';
            this.ffinal = '';
            this.finicial1 = '';
            this.ffinal1 = '';
        },
    },
};
</script>

<style scoped>
.reportes-page {
    padding: 8px 4px 30px;
}
.reportes-page h4,
.reportes-page h5 {
    color: #334e68;
}
.report-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
}
.report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1.2rem rgba(0, 0, 0, .08) !important;
}
.report-card .card-header {
    padding: 20px 22px 10px;
}
.report-card .card-body {
    padding: 18px 22px 24px;
}
.report-card .card-footer {
    padding: 0 22px 20px;
}
.report-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 14px;
    font-size: 20px;
}
.form-label {
    font-weight: 600;
    color: #52667a;
    margin-bottom: 6px;
}
.form-control {
    border-radius: 8px;
    min-height: 40px;
}
.btn {
    border-radius: 8px;
    font-weight: 600;
}
</style>
