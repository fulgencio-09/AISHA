<template>
    <main>
        <div class="container">
            <div class="card">
                <form v-on:submit.prevent="agregar()" method="post">
                    <div class="card-body ">
                        <h6 align="center" class=" p-1 border col-14 col-md-12 ">Listado de pagos relizados</h6>
                        <div class="row border">
                            <div class="col  col-sm-5 input-group-sm mb-4">
                                <label for="nameWithTitle" class="form-label">Fecha Inicial</label>
                                <input type="date" id="nameWithTitle" class="form-control" v-model="finicial">
                            </div>
                            <div class="col  col-sm-5 input-group-sm mb-4">
                                <label for="nameWithTitle" class="form-label">Fecha Final</label>
                                <input type="date" id="nameWithTitle" class="form-control" v-model="ffinal">
                            </div>
                        </div>
                        <div class="modal-footer  p-0">
                            <button type="submit" class="btn-sm  col-sm-3 btn-outline-success mt-2"><i
                                    class="fa fa-check edu-checked-pro" aria-hidden="true"></i> Exportar</button>

                        </div>
                    </div>
                </form>
                <form v-on:submit.prevent="exportar()" method="post">
                    <div class="card-body ">
                        <h6 align="center" class=" p-1 border col-14 col-md-12 ">Listado de estudiantes matriculados
                        </h6>
                        <div class="row border">
                            <div class="col  col-sm-5 input-group-sm mb-4">
                                <label for="nameWithTitle" class="form-label">Fecha Inicial</label>
                                <input type="date" id="nameWithTitle" class="form-control" v-model="finicial1">
                            </div>
                            <div class="col  col-sm-5 input-group-sm mb-4">
                                <label for="nameWithTitle" class="form-label">Fecha Final</label>
                                <input type="date" id="nameWithTitle" class="form-control" v-model="ffinal1">
                            </div>
                        </div>
                        <div class="modal-footer  p-0">
                            <button type="submit" class="btn-sm  col-sm-3 btn-outline-success mt-2"><i
                                    class="fa fa-check edu-checked-pro" aria-hidden="true"></i> Exportar</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
            :is-full-page="fullPage" />
    </main>
</template>
<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { utils, writeFileXLSX } from 'xlsx';
import { ref } from "vue";
import exportFromJSON from 'export-from-json';

export default {
    el: '#app',
    components: {
        Loading,
    },
    data() {
        return {
            name: '',
            isLoading: false,
            finicial: '',
            ffinal: '',
            reporte: [],
        }
    },
    methods: {

        agregar() {
            this.isLoading = true;
            axios.post('/exportar', {
                finicial: this.finicial,
                ffinal: this.ffinal,
            })
                .then((res) => {
                    this.reporte = res.data.reporte;
                    const rows = ref(this.reporte);                 
                    const wb = utils.book_new();
                    const ws = utils.json_to_sheet(rows.value);
                    ws["A1"].s = {
                        font: {
                            name: "Arial",
                            sz: 12,
                            bold: true,
                            color: { rgb: "FFFFAA00" },
                        },
                    };                    
                    utils.book_append_sheet(wb, ws, 'BASE DE DATOS');
                    writeFileXLSX(wb, "Listado de cuotas por estudiantes.xlsx");
                    this.isLoading = false
                }).catch((error) => {
                    this.asistencial = [];
                    this.isLoading = false
                })
        },
        exportar() {
            this.isLoading = true;
            axios.post('/exportar_', {
                finicial: this.finicial1,
                ffinal: this.ffinal1,
            })
                .then((res) => {
                    this.reporte = res.data.reporte;
                    const data = this.reporte;
                    const fileName = 'Listado de estudiantes matriculados';
                    const exportType = exportFromJSON.types.xls;
                    exportFromJSON({ data, fileName, exportType })
                    this.isLoading = false
                }).catch((error) => {
                    this.asistencial = [];
                    this.isLoading = false
                })
        }
    }
}
</script>
