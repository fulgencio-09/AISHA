<template>
    <main class="main abonos-page" id="main">
        <div class="container-fluid py-3">

            <!-- LISTADO DE MATRICULADOS -->
            <section v-show="vista === 'lista'" class="abonos-card">
                <div class="section-title">Matriculados</div>

                <div class="table-responsive">
                    <table class="table abonos-table mb-0">
                        <thead>
                            <tr>
                                <th>DOCUMENTO</th>
                                <th>NOMBRE</th>
                                <th>SEDE</th>
                                <th>AÑO</th>
                                <th>PERÍODO</th>
                                <th>SEMESTRE</th>
                                <th>PENDIENTE</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
                            </tr>
                            <tr class="filter-row">
                                <th>
                                    <input type="search" class="form-control form-control-sm" v-model="identidad"
                                        @input="busca" placeholder="Documento">
                                </th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in abonos" :key="item.id">
                                <td>{{ item.documento }}</td>
                                <td class="text-uppercase">{{ item.name }}</td>
                                <td>{{ item.sede }}</td>
                                <td>{{ item.año }}</td>
                                <td>{{ item.periodo }}</td>
                                <td>{{ nombreSemestreItem(item.semestre) }}</td>
                                <td>{{ formatoCOP(item.valor) }}</td>
                                <td>
                                    <span :class="Number(item.valor) > 0 ? 'status-pending' : 'status-paid'">
                                        {{ Number(item.valor) > 0 ? 'POR PAGAR' : 'PAGADO' }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button class="btn btn-success btn-sm action-btn" title="Agregar pagos"
                                        @click="abrirmodaledit(item)">
                                        <i class="fa fa-usd"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm action-btn" title="Pagos realizados"
                                        @click="pagado(item)">
                                        <i class="fa fa-clipboard"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!abonos.length">
                                <td colspan="9" class="empty-row">No se encontraron matrículas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="total-label">Total: <strong>{{ pagination.total || 0 }}</strong></div>
                    <nav v-if="pagesNumber.length">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">«</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="{ active: page === isActived }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">»</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>

            <!-- FORMULARIO DE ABONO -->
            <section v-show="vista === 'formulario'" class="abonos-card form-card">
                <div class="section-title">Formulario para la realización de aportes de pagos</div>

                <div class="form-panel">
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="field-label">AÑO</label>
                            <input type="text" class="form-control" :value="año" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="field-label">PERÍODO</label>
                            <input type="text" class="form-control" :value="periodo" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="field-label">SEMESTRE</label>
                            <input type="text" class="form-control" :value="nombreSemestre" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="field-label">CRÉDITO</label>
                            <input type="text" class="form-control" :value="creditoFormateado" readonly>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="field-label">FECHA</label>
                            <input type="date" :min="hoy" class="form-control" v-model="fechas">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="field-label">FORMA PAGO</label>
                            <select class="form-select" v-model="formapago">
                                <option value="">Seleccione</option>
                                <option value="Bancolombia">Bancolombia</option>
                                <option value="Nequi">Nequi</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Davivienda">Davivienda</option>
                                <option value="Daviplata">Daviplata</option>
                                <option value="Escuela">Escuela</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="field-label">CANTIDAD</label>
                            <input type="text" placeholder="$" class="form-control" :value="precioFormateado"
                                @input="actualizarPrecio">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button v-if="btncrear" @click.prevent="agregar" type="button" class="btn btn-outline-success">
                        <i class="fa fa-floppy-o me-1"></i> Guardar
                    </button>
                    <button @click.prevent="volverLista" type="button" class="btn btn-outline-danger">
                        <i class="fa fa-ban me-1"></i> Cancelar
                    </button>
                </div>
            </section>

            <!-- LISTADO DE PAGOS -->
            <section v-show="vista === 'pagos'" class="abonos-card payments-card">
                <div class="section-title">Listado de pagos realizado por el estudiante</div>

                <div class="table-responsive">
                    <table class="table abonos-table mb-0">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>CANTIDAD</th>
                                <th>CAJERO</th>
                                <th>ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in abonolist" :key="item.id || index">
                                <td>{{ item.fechas }}</td>
                                <td>{{ formatoCOP(item.cantidad) }}</td>
                                <td>{{ item.nombre }}</td>
                                <td class="actions-cell">
                                    <button @click="imprimir(item)" class="btn btn-secondary btn-sm action-btn"
                                        title="Imprimir">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <button v-if="item.valor > 0 && seccion == 'Admin' && index === abonolist.length - 1"
                                        @click="confirmDelete(item)" class="btn btn-danger btn-sm action-btn" title="Anular">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!abonolist.length">
                                <td colspan="4" class="empty-row">No existen pagos registrados.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL</th>
                                <th>{{ formatoCOP(total) }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-actions">
                    <button @click.prevent="volverLista" class="btn btn-warning btn-sm">
                        <i class="fa fa-reply-all me-1"></i> Salir
                    </button>
                </div>
            </section>

            <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                :is-full-page="true" />
        </div>
    </main>
</template>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const users = document.head.querySelector('meta[name="user"]');
const userData = users ? JSON.parse(users.content) : {};
const seccion = userData.especialidad || '';
const user_id = userData.id || null;

export default {
    components: { Loading },

    data() {
        return {
            id: this.$route.params.id,
            name: '',
            documento: '',
            abonos: [],
            abonolist: [],
            pagos: [],
            errors: {},
            seccion,
            user_id,
            creditoSeleccionado: '',
            deuda: '',
            formapago: '',
            asignatura_id: '',
            cantidad: '',
            fechas: '',
            semestre: '',
            identidad: '',
            año: '',
            periodo: '',
            hoy: new Date().toISOString().split('T')[0],
            estado: '',
            isLoading: false,
            btncrear: false,
            vista: 'lista',
            pagination: {
                total: 0,
                current_page: 1,
                per_page: 10,
                last_page: 1,
                from: 0,
                to: 0,
            },
            offset: 2,
        };
    },

    computed: {
        total() {
            if (!Array.isArray(this.abonolist)) return 0;
            return this.abonolist.reduce((suma, item) => {
                const cantidad = Number(item.cantidad);
                return suma + (Number.isNaN(cantidad) ? 0 : cantidad);
            }, 0);
        },
        precioFormateado() {
            return Number(this.cantidad) > 0
                ? '$ ' + new Intl.NumberFormat('es-CO').format(this.cantidad)
                : '';
        },
        creditoFormateado() {
            return this.creditoSeleccionado !== '' ? this.formatoCOP(this.creditoSeleccionado) : '';
        },
        nombreSemestre() {
            return this.nombreSemestreItem(this.semestre);
        },
        isActived() {
            return this.pagination.current_page;
        },
        pagesNumber() {
            if (!this.pagination.last_page || this.pagination.last_page <= 1) return [];
            let from = this.pagination.current_page - this.offset;
            if (from < 1) from = 1;
            let to = from + (this.offset * 2);
            if (to > this.pagination.last_page) to = this.pagination.last_page;
            const pages = [];
            while (from <= to) pages.push(from++);
            return pages;
        },
    },

    mounted() {
        this.created(1);
    },

    methods: {
        nombreSemestreItem(semestre) {
            const nombres = {
                1: 'INTRODUCTORIO',
                2: 'PRIMER SEMESTRE',
                3: 'SEGUNDO SEMESTRE',
                4: 'TERCER SEMESTRE',
                5: 'CUARTO SEMESTRE',
            };
            return nombres[Number(semestre)] || 'SEMESTRE NO VÁLIDO';
        },

        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
            }).format(Number(valor) || 0);
        },

        actualizarPrecio(event) {
            const valorLimpio = event.target.value.replace(/[^\d]/g, '');
            this.cantidad = parseInt(valorLimpio, 10) || 0;
        },

        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.created(page);
        },

        async created(page = 1) {
            this.isLoading = true;
            try {
                const res = await axios.get('/abono?page=' + page);
                this.abonos = res.data.abono?.data || [];
                this.pagination = res.data.pagination || this.pagination;
                this.identidad = '';
                this.vista = 'lista';
                this.btncrear = false;
            } catch (error) {
                this.abonos = [];
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        async busca() {
            if (!this.identidad) {
                this.created(1);
                return;
            }
            this.isLoading = true;
            try {
                const res = await axios.post('/abono/busca', { identidad: this.identidad });
                this.abonos = res.data.abono?.data || [];
                this.pagination = res.data.pagination || this.pagination;
                this.vista = 'lista';
            } catch (error) {
                this.abonos = [];
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        async credit() {
            this.isLoading = true;
            try {
                const response = await axios.get('/abono/credito/' + this.asignatura_id);
                const cred = response.data.cred || [];
                if (cred.length) {
                    this.name = cred[0].name;
                    this.documento = cred[0].documento;
                    this.deuda = Number(cred[0].valor) || 0;
                    this.creditoSeleccionado = Number(cred[0].valor) || 0;
                    this.año = cred[0].año;
                    this.periodo = cred[0].periodo;
                    this.semestre = cred[0].semestre;
                    this.telefono = cred[0].telefono;
                } else {
                    this.mostrarError({ response: { data: { message: 'No se encontró el crédito seleccionado.' } } });
                }
            } catch (error) {
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        async agregar() {
            if (!this.asignatura_id) return this.$toastr?.warning?.('El crédito es obligatorio');
            if (!this.fechas) return this.$toastr?.warning?.('El campo fecha es obligatorio');
            if (!this.cantidad) return this.$toastr?.warning?.('El campo cantidad es obligatorio');
            if (!this.formapago) return this.$toastr?.warning?.('El campo forma de pago es obligatorio');
            if (Number(this.cantidad) > Number(this.creditoSeleccionado)) {
                return this.$toastr?.warning?.('El valor a abonar no puede superar el crédito pendiente.');
            }

            this.isLoading = true;
            const saldoAnterior = Number(this.creditoSeleccionado) || 0;
            const saldoCalculado = saldoAnterior - Number(this.cantidad);

            try {
                const res = await axios.post('/abono', {
                    asignatura_id: this.asignatura_id,
                    cantidad: this.cantidad,
                    semestre: this.semestre,
                    fechas: this.fechas,
                    formapago: this.formapago,
                    documento: this.documento,
                    deuda: saldoCalculado,
                    user_id: this.user_id,
                    name: this.name,
                    estado: 1,
                });

                const saldoRestante = Number(res.data?.input?.deuda ?? saldoCalculado) || 0;
                this.creditoSeleccionado = saldoRestante;
                this.deuda = saldoRestante;
                this.abonolist = Array.isArray(res.data) ? res.data : this.abonolist;
                this.cantidad = '';
                this.fechas = '';
                this.formapago = '';
                this.vista = 'pagos';
                this.toastSuccess(res.data?.message || 'Se abonó exitosamente.');
            } catch (error) {
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        abrirmodaledit(datos) {
            this.fechas = '';
            this.cantidad = '';
            this.formapago = '';
            this.asignatura_id = datos.asignatura_id;
            this.btncrear = true;
            this.vista = 'formulario';
            this.credit();
        },

        async pagado(datos) {
            this.id = datos.id;
            this.año = datos.año;
            this.periodo = datos.periodo;
            this.asignatura_id = datos.asignatura_id;
            this.vista = 'pagos';
            await this.list();
        },

        async list() {
            this.isLoading = true;
            try {
                const res = await axios.post('/abono/list', {
                    id: this.id,
                    asignatura_id: this.asignatura_id,
                });
                this.abonolist = res.data.abono || [];
            } catch (error) {
                this.abonolist = [];
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        async confirmDelete(item) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            });
            if (result.isConfirmed) this.eliminar(item);
        },

        async eliminar(item) {
            this.isLoading = true;
            try {
                await axios.get(`/abono/eliminar/${item.id}/${this.user_id}`);
                await Swal.fire('Eliminado', 'El registro fue eliminado', 'success');
                await this.list();
            } catch (error) {
                this.mostrarError(error);
            } finally {
                this.isLoading = false;
            }
        },

        volverLista() {
            this.created(1);
        },

        imprimir(item) {
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            const sem = this.nombreSemestreItem(item.semestre);
            const contenido = `
                <html><head><title>Factura de Pago</title>
                <style>
                    body{font-family:Arial;padding:20px;font-size:14px}table{width:100%;border-collapse:collapse;margin-top:15px;font-size:12px}th,td{border:1px solid #ccc;padding:8px;text-align:left}#ti{text-align:center;font-size:12px}#im{text-align:right;font-size:12px}
                </style></head><body>
                <img src="${logoUrl}" style="max-height:60px;width:100%;object-fit:contain">
                <p id="ti">Fundación para el Desarrollo Educativo y Social de la Guajira</p>
                <p id="ti"><strong>NIT:</strong> 901691754-7</p>
                <h3>Factura de Pago</h3>
                <p id="im"><strong>Fecha de impresión:</strong> ${new Date().toLocaleDateString('es-CO')}</p>
                <p><strong>Estudiante:</strong> ${item.name || this.name}</p>
                <table><thead><tr><th>Descripción</th><th>Forma Pago</th><th>Fecha</th><th>Por Pagar</th><th>Valor cancelado</th></tr></thead>
                <tbody><tr><td>Pago de matrícula perteneciente al ${sem}</td><td>${item.formapago || ''}</td><td>${item.fechas || ''}</td><td>${this.formatoCOP(item.deuda)}</td><td>${this.formatoCOP(item.cantidad)}</td></tr></tbody>
                <tfoot><tr><th colspan="4">TOTAL</th><th>${this.formatoCOP(item.cantidad)}</th></tr></tfoot></table>
                <br><p><strong>Anyelis Deluque Galván</strong><br>Representante Legal</p>
                <div style="text-align:center;font-size:9px">RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013<br>Riohacha, La Guajira</div>
                </body></html>`;
            const ventana = window.open('', '_blank');
            if (!ventana) return;
            ventana.document.write(contenido);
            ventana.document.close();
            ventana.onload = () => setTimeout(() => { ventana.focus(); ventana.print(); ventana.close(); }, 800);
        },

        async enviar(item) {
            try {
                await axios.post('/facturas', {
                    to: '57' + item.telefono,
                    name: item.name,
                    documento: item.documento,
                    semestre: item.semestre,
                    formapago: item.formapago,
                    fecha: item.fechas,
                    deuda: item.deuda,
                    cantidad: item.cantidad,
                });
                this.toastSuccess('Mensaje enviado con éxito a WhatsApp.');
            } catch (error) {
                this.mostrarError(error);
            }
        },

        mostrarError(error) {
            const message = error?.response?.data?.message || error?.response?.data?.error || 'Error al procesar la solicitud.';
            if (typeof toastr !== 'undefined') toastr.error(message);
        },

        toastSuccess(message) {
            if (typeof toastr !== 'undefined') toastr.success(message);
        },

        onCancel() {
            this.isLoading = false;
        },
    },
};
</script>

<style scoped>
.abonos-page {
    background: #f5f7f9;
    min-height: 100%;
    color: #50627a;
}

.abonos-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(38, 50, 56, .10);
    padding: 24px;
    margin-bottom: 18px;
}

.section-title {
    border: 1px solid #d8dee6;
    min-height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    color: #566b84;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: .2px;
    margin-bottom: 16px;
    background: #fff;
}

.abonos-table {
    border: 1px solid #d8dee6;
    font-size: 12px;
    color: #52677f;
    vertical-align: middle;
}

.abonos-table th,
.abonos-table td {
    border-color: #d8dee6 !important;
    padding: 10px 12px;
    white-space: nowrap;
    text-align: center;
}

.abonos-table thead th {
    background: #fff;
    color: #52677f;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: .5px;
}

.abonos-table tbody td {
    font-weight: 600;
    font-size: 12px;
}

.abonos-table tbody tr:hover {
    background: #fafbfc;
}

.filter-row th {
    padding: 8px 20px;
    background: #fff;
}

.filter-row input {
    max-width: 175px;
    margin: 0 auto;
    border-color: #d4dce5;
    font-size: 12px;
}

.status-pending,
.status-paid {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .3px;
}

.status-pending {
    background: #e8e5ff;
    color: #6961d8;
}

.status-paid {
    background: #e5f7ea;
    color: #2f9b55;
}

.action-btn {
    width: 32px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    border-radius: 4px;
}

.actions-cell {
    min-width: 95px;
}

.empty-row {
    padding: 24px !important;
    color: #8b98a8;
    font-weight: 500 !important;
}

.table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
}

.total-label {
    font-size: 12px;
    color: #65768a;
}

.pagination .page-link {
    color: #66778c;
    border-color: #e0e5ea;
    min-width: 28px;
    text-align: center;
    font-size: 12px;
}

.pagination .active .page-link {
    background: #6868ee;
    border-color: #6868ee;
    color: #fff;
}

.form-panel {
    border: 1px solid #d8dee6;
    padding: 16px;
}

.field-label {
    display: block;
    margin-bottom: 6px;
    color: #596d85;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.form-control,
.form-select {
    border-color: #d5dde6;
    color: #5d7188;
    font-size: 12px;
    min-height: 38px;
    box-shadow: none !important;
}

.form-control:focus,
.form-select:focus {
    border-color: #aebbc9;
}

.form-control[readonly] {
    background: #eef1f4;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    border-top: 1px solid #d8dee6;
    margin-top: 18px;
    padding-top: 16px;
}

.form-actions .btn {
    font-size: 12px;
}

.payments-card {
    margin-top: 0;
}

@media (max-width: 768px) {
    .abonos-card {
        padding: 14px;
    }

    .table-footer {
        align-items: flex-start;
        gap: 10px;
        flex-direction: column;
    }

    .section-title {
        font-size: 11px;
    }
}
</style>