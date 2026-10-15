<template>
    <main class="main notas-page" id="main">
        <div class="container-fluid py-3">

            <!-- LISTADO DE MATRICULADOS -->
            <section class="notas-card" id="listado">
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
                                    <button v-if="items.mat_matr === 'No'" @click="materia(items)"
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
            <section class="notas-card gestion-card" id="dos">
                <div class="section-title">Gestión de Notas</div>

                <form @submit.prevent="guardarNota">
                    <div class="form-panel">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="field-label">MATERIA</label>
                                <input type="text" v-model="form.materia" class="form-control" disabled required>
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
                                <td>{{ (nota.corte3 * .35 + nota.corte1 * .35 + nota.corte2 * .30) ?? '-' }}</td>
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
    data() {
        return {
            salir: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            onCancel: this.onCancel,
            identidad: '',
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,
            },
            offset: 2,
            nota: [],
            notas: [],
            form: {
                semestre: '',
                materia: '',
                materias: '',
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
        this.created()
    },
    methods: {
        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },
        imprimirTabla() {
            window.print(table);
        },
        async consulta(items) {
            const res = await axios.get(`/notas/${items.asignatura_id}`, this.form,)
            this.notas = res.data
            this.imprimir(this.notas)
        },
        imprimir(notas) {
            if (this.notas.length > 0) {
                console.log(this.notas)
                var sem = this.notas.semestre
            }

            const logoUrl = `${window.location.origin}/images/aishap.png`;
            if (notas.semestre == 1) {
                var sem = "Primer";
            } else if (notas.semestre == 2) {
                var sem = "Segundo";
            } else if (notas.semestre == 3) {
                var sem = "Tercer";
            } else if (notas.semestre == 4) {
                var sem = "Cuarto";
            } else {
                var sem = "Quinto"
            }

            const contenido = `
<html>
<head>
<title align="center">Factura de Pago</title>
<style>
body { font-family: Arial; padding: 20px; font-size: 8px;}
h2 { margin-bottom: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 10px;font-size: 6px; }
th { border-bottom: 1px solid black; }
th { border-top: 1px solid black; }
#ti { text-align: center; }
#im { text-align: right; }
.linea { margin: 10px 0; border: none; border-top: 1px solid #ccc; }
.header { margin-bottom: 1px; }
.item { margin-bottom: 1px; }
</style>
</head>
<body>
<div class="header">
<img src="${logoUrl}" alt="Logo" style="max-height: 60px; margin-right:30px;">
<p id="ti">Fundacion para el Desarrollo Educativo y Social de la Guajira </p>
<p id="ti"><strong>NIT:</strong> 901691754-7</p>
<h2>Factura de Pago</h2>
<p id="im"><strong>Fecha de impresión:</strong> ${new Date().toLocaleDateString('es-CO')}</p>
<div class="info-cliente">
<p><strong>Estudiante:</strong> ${notas.name}</p>
<p><strong>Documento:</strong> ${notas.documento}</p>
</div>
</div>
<div class="factura-container">
<table>
<thead>
<tr>
<th>Semestre</th>
<th>Materia</th>
<th>Primer corte</th>
<th>Segundo corte</th>
<th>Tercer Corte</th>
</tr>
</thead>
<tbody>
<tr>
<th>Pago de matricula perteneciente al ${sem} Semestre</th>
<th>notas.materia</th>
</tr>
</tbody>
<tfoot>
<tr><td></td><th>Total:</th></tr>
</tfoot>
<tfoot>
<tr><td></td><td>Sub Total:</td><td></td></tr>
</tfoot>
<tfoot>
<tr><td></td><td>Impuestos(0%):</td><td>$ 0</td></tr>
</tfoot>
</table>
</div>
<br><br><br>
<div style="text-align: left;">
<img src="${window.location.origin}/images/firma.png" alt="Firma" style="width: 100px; height: auto;">
<p style="font-size: 8px; margin: 0;"><strong>Anyelis Deluque Galván</strong></p>
<p style="font-size: 8px; margin: 0;">Representante Legal</p>
</div>
<div style="text-align: center; margin-top:40px;">
<p style="font-size: 8px; margin: 0;"><strong>RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013</strong></p>
<p style="font-size: 8px; margin: 0;">Cra. 18 No. 21 - 76\tCel.: 302 752 1119\tfundescguajira@gmail.com</p>
<p style="font-size: 8px; margin: 0;">Riohacha, La Guajira</p>
</div>
</body>
</html>
`;
            const ventana = window.open('', '_blank');
            ventana.document.write(contenido);
            ventana.document.close();
            ventana.onload = () => {
                setTimeout(() => {
                    ventana.focus();
                    ventana.print();
                    ventana.close();
                }, 800);
            };
        },
        buscar() {
            this.isLoading = true;
            axios.post('/notas/buscar', {
                identidad: this.identidad,
            })
                .then((res) => {
                    this.nota = res.data.nota.data
                    this.pagination = res.data.pagination
                    this.isLoading = false
                }).catch((error) => {
                    this.nota = [];
                    this.isLoading = false
                })
        },
        async created(page) {
            dos.hidden = true;
            this.isLoading = true;
            try {
                const res = await axios.get('/nota?page=' + page);
                this.nota = res.data.nota.data;
                this.pagination = res.data.pagination;
                this.isLoading = false;
                this.semestre = '';
                this.id = '';
                this.estado = '';
                this.btncrear = true;
                this.btnedit = false;
            } catch (error) {
                this.isLoading = false;
                console.error(error);
            }
        },
        ocultar() {
            dos.hidden = true
            main.hidden = false
            this.created()
        },
        materia(items) {
            this.isLoading = true;
            axios.post('/agregar/materia', {
                semestre: items.semestre,
                matricula: items.asignatura_id,
            })
                .then((res) => {
                    this.notas = res.data.notas;
                    this.created()
                    this.isLoading = false
                }).catch((error) => {
                    this.nota = [];
                    this.isLoading = false
                })
        },
        async getNotas(items) {
            this.salir = false
            dos.hidden = false;
            main.hidden = true;
            const res = await axios.get(`/notas/${items.asignatura_id}`, this.form,)
            this.notas = res.data
        },
        guardarNota() {
            this.isLoading = true;
            axios.put(`/nota/${this.idEdit}`, this.form)
                .then((res) => {
                    this.notas = res.data
                    this.getNotas(this.notas)
                    console.log(this.notas)
                    this.isLoading = false
                }).catch((error) => {
                    this.nota = [];
                    this.isLoading = false
                })
        },
        async guardarNotai() {
            await axios.put(`/nota/${this.idEdit}`, this.form)
            this.getNotas()
            this.resetForm()
        },
        editarNota(nota) {
            this.form = { ...nota }
            this.idEdit = nota.id
            console.log(this.form)
            this.editando = true
            this.salir = false
        },
        cancelarEdicion() {
            this.resetForm()
        },
        resetForm() {
            this.form = {
                semestre: '',
                materia: '',
                corte1: null,
                corte2: null,
                corte3: null,
                habilitada_recuperacion: false
            }
            this.editando = false
            this.salir = true
            this.idEdit = null
        },
        async eliminarNota(id) {
            if (confirm('¿Eliminar esta nota?')) {
                await axios.delete(`/notas/${id}`)
                this.getNotas()
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

.notas-table tbody tr:hover {
    background: #fafbfc;
}

.filter-row th {
    padding: 8px 20px;
}

.filter-row input {
    min-height: 28px;
    border: 1px solid #d7dde5;
    box-shadow: none;
}

.actions-cell {
    white-space: nowrap;
}

.action-btn {
    width: 36px;
    height: 30px;
    padding: 0;
    margin: 0 2px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 14px;
}

.total-label {
    color: #65758b;
    font-size: 13px;
}

.pagination .page-link {
    color: #65758b;
    border-color: #e1e6eb;
    min-width: 30px;
    text-align: center;
}

.pagination .page-item.active .page-link {
    background: #686cff;
    border-color: #686cff;
    color: #fff;
}

.form-panel {
    border: 1px solid #d7dde5;
    padding: 16px 10px;
}

.field-label {
    display: block;
    color: #536b86;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.form-panel .form-control {
    border-color: #d7dde5;
    min-height: 38px;
    color: #536b86;
    box-shadow: none;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #d7dde5;
}

.empty-row {
    color: #8a98a9 !important;
    padding: 18px !important;
}

@media (max-width: 768px) {
    .notas-card {
        padding: 12px;
    }

    .table-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .notas-table th,
    .notas-table td {
        font-size: 11px;
    }
}
</style>