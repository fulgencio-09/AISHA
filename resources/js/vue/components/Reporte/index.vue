<template>
    <div class="container-fluid reporte-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fa fa-bar-chart me-2"></i>Reportes</h4>
                <p class="text-muted mb-0">Consulta el estado de las matrículas y genera los certificados de pago.</p>
            </div>
            <button class="btn btn-outline-secondary" @click="limpiarBusqueda">
                <i class="fa fa-eraser me-1"></i> Limpiar búsqueda
            </button>
        </div>

        <div class="card border-0 shadow-sm mb-4 filter-card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Buscar estudiante por documento</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                            <input type="text" class="form-control" v-model="identidad" @keyup.enter="busca" placeholder="Digite el número de documento">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-primary flex-fill" @click="busca" :disabled="isLoading">
                            <i class="fa fa-search me-1"></i> Buscar
                        </button>
                        <button class="btn btn-outline-secondary flex-fill" @click="limpiarBusqueda">
                            <i class="fa fa-refresh me-1"></i> Todos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Matrículas y estado académico</h5>
                    <small class="text-muted">Total de registros: {{ pagination.total || 0 }}</small>
                </div>
                <span class="report-badge"><i class="fa fa-list me-1"></i>Listado</span>
            </div>

            <div v-if="isLoading" class="text-center py-5 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <div class="mt-2">Cargando reportes...</div>
            </div>

            <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Año</th>
                            <th>Semestre</th>
                            <th>Pendiente</th>
                            <th>Valor pagado</th>
                            <th>Estado semestral</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in abono" :key="item.id">
                            <td class="fw-semibold">{{ item.documento }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.año }}</td>
                            <td>{{ nombreSemestre(item.semestre) }}</td>
                            <td>{{ formatoCOP(item.valor) }}</td>
                            <td>{{ formatoCOP(item.total) }}</td>
                            <td>
                                <span v-if="Number(item.estad) > 0" class="badge bg-primary-subtle text-primary">Abierto</span>
                                <span v-else class="badge bg-secondary-subtle text-secondary">Cerrado</span>
                            </td>
                            <td>
                                <span v-if="Number(item.estado) > 0" class="badge bg-warning-subtle text-warning">Por pagar</span>
                                <span v-else class="badge bg-success-subtle text-success">Pagado</span>
                            </td>
                            <td class="text-center text-nowrap">
                                <button @click="reporte(item)" class="btn btn-sm btn-secondary me-1" title="Imprimir certificado">
                                    <i class="fa fa-print"></i>
                                </button>
                                <button v-if="Number(item.estad) === 1 && seccion === 'Admin'" @click="cerrar(item)" class="btn btn-sm btn-danger" title="Cerrar matrícula">
                                    <i class="fa fa-lock"></i>
                                </button>
                                <button v-else-if="Number(item.estad) === 0 && seccion === 'Admin'" @click="abrir(item)" class="btn btn-sm btn-success" title="Abrir matrícula">
                                    <i class="fa fa-unlock"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!abono.length">
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa fa-folder-open-o fa-2x d-block mb-2"></i>
                                No hay registros para mostrar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white" v-if="pagination.last_page > 1">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted">Página {{ pagination.current_page }} de {{ pagination.last_page }}</small>
                    <nav aria-label="Paginación de reportes">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                <button class="page-link" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">Anterior</button>
                            </li>
                            <li v-for="page in pagesNumber" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                                <button class="page-link" @click="changePage(page)">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                <button class="page-link" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">Siguiente</button>
                            </li>
                        </ul>
                    </nav>
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

const metaUser = document.head.querySelector('meta[name="user"]');
const seccionUsuario = metaUser ? JSON.parse(metaUser.content).especialidad : '';

export default {
    name: 'ReporteMatriculas',
    components: { Loading },
    data() {
        return {
            abono: [],
            identidad: '',
            seccion: seccionUsuario,
            isLoading: false,
            asignatura_id: '',
            pagination: { total: 0, current_page: 1, per_page: 10, last_page: 1, from: 0, to: 0 },
            offset: 2,
        };
    },
    computed: {
        pagesNumber() {
            if (!this.pagination.last_page || this.pagination.last_page <= 1) return [];
            let from = Math.max(1, this.pagination.current_page - this.offset);
            let to = Math.min(this.pagination.last_page, from + (this.offset * 2));
            if (to - from < this.offset * 2) from = Math.max(1, to - (this.offset * 2));
            const pages = [];
            for (let page = from; page <= to; page++) pages.push(page);
            return pages;
        },
    },
    mounted() {
        this.created(1);
    },
    methods: {
        nombreSemestre(semestre) {
            const nombres = { 1: 'Introductorio', 2: 'Primer semestre', 3: 'Segundo semestre', 4: 'Tercer semestre', 5: 'Cuarto semestre' };
            return nombres[Number(semestre)] || '—';
        },
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(Number(valor) || 0);
        },
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.created(page);
        },
        async created(page = 1) {
            this.isLoading = true;
            try {
                const res = await axios.get('/reporte?page=' + page);
                this.abono = res.data.abono ? res.data.abono.data : [];
                this.pagination = res.data.pagination || this.pagination;
            } catch (error) {
                this.abono = [];
                toastr.error('No fue posible cargar el reporte.');
            } finally {
                this.isLoading = false;
            }
        },
        busca() {
            if (!this.identidad) {
                this.created(1);
                return;
            }
            this.isLoading = true;
            axios.post('/reporte/busca', { identidad: this.identidad })
                .then((res) => {
                    this.abono = res.data.abono ? res.data.abono.data : [];
                    this.pagination = res.data.pagination || this.pagination;
                })
                .catch(() => {
                    this.abono = [];
                    toastr.error('No fue posible realizar la búsqueda.');
                })
                .finally(() => { this.isLoading = false; });
        },
        limpiarBusqueda() {
            this.identidad = '';
            this.created(1);
        },
        reporte(datos) {
            this.asignatura_id = datos.asignatura_id;
            this.isLoading = true;
            axios.post('/abono/pdf', { asignatura_id: this.asignatura_id })
                .then((res) => {
                    this.imprimir(res.data.pdf || []);
                })
                .catch(() => {
                    toastr.error('No fue posible generar el certificado.');
                })
                .finally(() => { this.isLoading = false; });
        },
        imprimir(item) {
            if (!item || !item.length) {
                toastr.warning('No existen pagos para generar el certificado.');
                return;
            }
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            const total = item.reduce((suma, abono) => suma + (Number(abono.cantidad) || 0), 0);
            let filas = '';
            item.forEach((abono) => {
                filas += `<tr><td>${this.nombreSemestre(abono.semestre)}</td><td>${abono.formapago || ''}</td><td>${abono.fechas || ''}</td><td>${this.formatoCOP(abono.porpagar || 0)}</td><td>${this.formatoCOP(abono.cantidad || 0)}</td></tr>`;
            });
            const fecha = new Date();
            const fechaLarga = `a los ${fecha.getDate()} días del mes de ${fecha.toLocaleString('es-CO', { month: 'long' })} de ${fecha.getFullYear()}`;
            const contenido = `
                <html><head><title>Certificado de Paz y Salvo</title>
                <style>
                    body{font-family:Arial,sans-serif;padding:24px;font-size:14px;color:#222}
                    .logo{width:100%;height:75px;object-fit:contain;margin-bottom:10px}
                    h2{text-align:center;margin:18px 0}
                    .center{text-align:center}.right{text-align:right}
                    table{width:100%;border-collapse:collapse;margin-top:18px}
                    th,td{border:1px solid #333;padding:7px;text-align:left}
                    th{background:#f1f3f5}.text{font-size:15px;line-height:1.6}
                    .firma{margin-top:35px}.footer{text-align:center;margin-top:55px;font-size:12px}
                </style></head><body>
                <img class="logo" src="${logoUrl}" alt="Logo">
                <div class="center">Fundación para el Desarrollo Educativo y Social de la Guajira</div>
                <div class="center"><strong>NIT:</strong> 901691754-7</div>
                <h2>CERTIFICADO DE PAZ Y SALVO</h2>
                <p class="right"><strong>Fecha de impresión:</strong> ${fecha.toLocaleDateString('es-CO')}</p>
                <p class="text">Yo, Angelis Deluque Galván, actuando en calidad de Representante Legal de la <strong>Fundación para el Desarrollo Educativo y Social de la Guajira (AISHA S.A.S.)</strong>, identificada con NIT No. <strong>901691754-7</strong>, certifico que:</p>
                <p class="text">El(la) estudiante <strong>${item[0].name}</strong>, identificado(a) con documento No. <strong>${item[0].documento}</strong>, se encuentra a paz y salvo, sin obligación pendiente con nuestra entidad, correspondiente al semestre académico ${item[0].año}-${item[0].periodo}.</p>
                <table><thead><tr><th>Semestre</th><th>Forma de pago</th><th>Fecha de pago</th><th>Por pagar</th><th>Valor cancelado</th></tr></thead><tbody>${filas}</tbody><tfoot><tr><td colspan="3"></td><th>Total:</th><th>${this.formatoCOP(total)}</th></tr></tfoot></table>
                <p class="text">Este certificado se expide a solicitud del interesado y para los fines que estime convenientes, ${fechaLarga}.</p>
                <div class="firma"><img src="${window.location.origin}/images/firma.png" alt="Firma" style="width:100px"><p><strong>Anyelis Deluque Galván</strong></p><p>Representante Legal</p></div>
                <div class="footer"><p><strong>RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013</strong></p><p>Cra. 18 No. 21 - 76 Cel.: 302 752 1119 fundescguajira@gmail.com</p><p>Riohacha, La Guajira</p></div>
                </body></html>`;
            const ventana = window.open('', '_blank');
            ventana.document.write(contenido);
            ventana.document.close();
            ventana.onload = () => setTimeout(() => { ventana.focus(); ventana.print(); ventana.close(); }, 800);
        },
        cerrar(datos) {
            if (!confirm('¿Seguro que desea cerrar la matrícula?')) return;
            this.isLoading = true;
            axios.get('/cerrar/' + datos.asignatura_id)
                .then(() => { toastr.success('Matrícula cerrada exitosamente.'); this.created(this.pagination.current_page); })
                .catch(() => toastr.error('No fue posible cerrar la matrícula.'))
                .finally(() => { this.isLoading = false; });
        },
        abrir(datos) {
            if (!confirm('¿Seguro que desea abrir la matrícula?')) return;
            this.isLoading = true;
            axios.get('/abrir/' + datos.asignatura_id)
                .then(() => { toastr.success('Matrícula abierta exitosamente.'); this.created(this.pagination.current_page); })
                .catch(() => toastr.error('No fue posible abrir la matrícula.'))
                .finally(() => { this.isLoading = false; });
        },
    },
};
</script>

<style scoped>
.reporte-page { padding: 8px 4px 30px; }
.reporte-page h4, .reporte-page h5 { color: #334e68; }
.filter-card, .reporte-page > .card { border-radius: 12px; overflow: hidden; }
.filter-card .card-body { padding: 20px 22px; }
.form-label { font-weight: 600; color: #52667a; margin-bottom: 6px; }
.form-control { border-radius: 8px; min-height: 40px; }
.input-group-text { border-radius: 8px 0 0 8px; }
.btn { border-radius: 8px; font-weight: 600; }
.card-header { padding: 18px 22px; }
.card-footer { padding: 14px 22px; }
.report-badge { background: #eef2ff; color: #5b61d6; border-radius: 20px; padding: 6px 12px; font-size: 12px; font-weight: 600; }
.table { font-size: 13px; }
.table thead th { color: #52667a; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
.table tbody td { color: #43566b; }
.table tbody tr { transition: background-color .15s ease; }
.badge { font-weight: 600; padding: 6px 9px; }
.pagination .page-link { border-radius: 7px; margin: 0 2px; }
</style>
