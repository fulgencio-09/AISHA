<template>
    <div class="container-fluid seguro-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fa fa-shield me-2"></i>Seguro Estudiantil</h4>
                <p class="text-muted mb-0">Consulta y registra el pago único del seguro asociado a la matrícula.</p>
            </div>
            <button class="btn btn-primary" @click="cargarSeguros">
                <i class="fa fa-refresh me-1"></i> Actualizar
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Matrículas consultadas</small><h3 class="mb-0">{{ seguros.length }}</h3></div><span class="seguro-icon bg-primary-subtle text-primary"><i class="fa fa-list"></i></span></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Pendientes</small><h3 class="mb-0 text-warning">{{ pendientes }}</h3></div><span class="seguro-icon bg-warning-subtle text-warning"><i class="fa fa-clock-o"></i></span></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Pagados</small><h3 class="mb-0 text-success">{{ pagados }}</h3></div><span class="seguro-icon bg-success-subtle text-success"><i class="fa fa-check-circle"></i></span></div></div></div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4"><label class="form-label">Buscar estudiante</label><input v-model="filtros.buscar" type="text" class="form-control" placeholder="Documento o nombre"></div>
                    <div class="col-md-2"><label class="form-label">Año</label><input v-model="filtros.ano" type="number" class="form-control" placeholder="2026"></div>
                    <div class="col-md-2"><label class="form-label">Período</label><input v-model="filtros.periodo" type="text" class="form-control" placeholder="INTRODUCTORIO"></div>
                    <div class="col-md-2"><label class="form-label">Semestre</label><select v-model="filtros.semestre" class="form-select"><option value="">Todos</option><option v-for="semestre in [1,2,3,4,5]" :key="semestre" :value="semestre">{{ semestre }}</option></select></div>
                    <div class="col-md-2"><label class="form-label">Estado</label><select v-model="filtros.estado" class="form-select"><option value="">Todos</option><option value="0">Pendiente</option><option value="1">Pagado</option></select></div>
                </div>
                <div class="mt-3 d-flex gap-2"><button class="btn btn-primary" @click="buscar"><i class="fa fa-search me-1"></i> Buscar</button><button class="btn btn-outline-secondary" @click="limpiarFiltros"><i class="fa fa-eraser me-1"></i> Limpiar</button></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center"><strong>Matrículas / Seguro Estudiantil</strong><span class="text-muted small" v-if="meta.total">Total: {{ meta.total }}</span></div>
            <div v-if="cargando" class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><div class="mt-2 text-muted">Cargando matrículas...</div></div>
            <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Documento</th><th>Estudiante</th><th>Sede</th><th>Año</th><th>Período</th><th>Sem.</th><th>Valor seguro</th><th>Estado</th><th class="text-center">Acción</th></tr></thead>
                    <tbody>
                        <tr v-for="seguro in seguros" :key="seguro.asignatura_id">
                            <td>{{ estudiante(seguro).documento || '—' }}</td>
                            <td><div class="fw-semibold">{{ estudiante(seguro).name || 'Sin nombre' }}</div><small class="text-muted">Matrícula #{{ seguro.asignatura_id }}</small></td>
                            <td>{{ asignatura(seguro).sede || '—' }}</td><td>{{ asignatura(seguro).año || '—' }}</td><td>{{ asignatura(seguro).periodo || '—' }}</td><td>{{ asignatura(seguro).semestre || '—' }}</td>
                            <td class="fw-semibold">{{ seguro.seguro_registrado ? moneda(seguro.valor) : 'Sin registrar' }}</td>
                            <td><span v-if="seguro.seguro_registrado && Boolean(seguro.estado)" class="badge bg-success-subtle text-success">Pagado</span><span v-else-if="seguro.seguro_registrado" class="badge bg-warning-subtle text-warning">Pendiente</span><span v-else class="badge bg-secondary-subtle text-secondary">Sin registrar</span></td>
                            <td class="text-center">
                                <button v-if="!seguro.seguro_registrado" class="btn btn-sm btn-primary" @click="abrirRegistro(seguro)"><i class="fa fa-plus me-1"></i> Registrar</button>
                                <button v-else-if="!Boolean(seguro.estado)" class="btn btn-sm btn-success" @click="abrirPago(seguro)"><i class="fa fa-money me-1"></i> Pagar</button>
                                <button v-else class="btn btn-sm btn-outline-secondary" @click="verPago(seguro)"><i class="fa fa-eye me-1"></i> Ver</button>
                            </td>
                        </tr>
                        <tr v-if="!seguros.length"><td colspan="9" class="text-center py-5 text-muted"><i class="fa fa-shield fa-2x mb-2 d-block"></i>No hay matrículas con los filtros seleccionados.</td></tr>
                    </tbody>
                </table>
            </div>
            <div v-if="meta.last_page > 1" class="card-footer bg-white"><nav><ul class="pagination pagination-sm mb-0 justify-content-end"><li class="page-item" :class="{disabled: meta.current_page === 1}"><button class="page-link" @click="cambiarPagina(meta.current_page-1)">Anterior</button></li><li v-for="pagina in meta.last_page" :key="pagina" class="page-item" :class="{active: pagina === meta.current_page}"><button class="page-link" @click="cambiarPagina(pagina)">{{ pagina }}</button></li><li class="page-item" :class="{disabled: meta.current_page === meta.last_page}"><button class="page-link" @click="cambiarPagina(meta.current_page+1)">Siguiente</button></li></ul></nav></div>
        </div>

        <div v-if="modalRegistro" class="seguro-modal-backdrop" @click.self="cerrarRegistro">
            <div class="seguro-modal card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="fa fa-shield me-2"></i>Registrar seguro</h5><button type="button" class="btn-close btn-close-white" @click="cerrarRegistro"></button></div>
                <div class="card-body">
                    <div v-if="seguroSeleccionado" class="alert alert-light border"><div class="fw-semibold">{{ estudiante(seguroSeleccionado).name || 'Estudiante' }}</div><small>Documento: {{ estudiante(seguroSeleccionado).documento || '—' }}</small><div class="mt-2">Matrícula #{{ seguroSeleccionado.asignatura_id }}</div></div>
                    <label class="form-label">Valor del seguro <span class="text-danger">*</span></label><input v-model="formRegistro.valor" type="number" min="0" step="0.01" class="form-control" placeholder="Valor del seguro">
                    <label class="form-label mt-3">Observación</label><textarea v-model="formRegistro.observacion" class="form-control" rows="3"></textarea>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end gap-2"><button class="btn btn-outline-secondary" @click="cerrarRegistro" :disabled="guardando">Cancelar</button><button class="btn btn-primary" @click="guardarRegistro" :disabled="guardando"><i v-if="guardando" class="fa fa-spinner fa-spin me-1"></i><i v-else class="fa fa-check me-1"></i>{{ guardando ? 'Guardando...' : 'Registrar seguro' }}</button></div>
            </div>
        </div>

        <div v-if="modalPago" class="seguro-modal-backdrop" @click.self="cerrarModal">
            <div class="seguro-modal card shadow-lg border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="fa fa-money me-2"></i>Registrar pago del seguro</h5><button type="button" class="btn-close btn-close-white" @click="cerrarModal"></button></div>
                <div class="card-body">
                    <div v-if="seguroSeleccionado" class="alert alert-light border mb-4"><div class="fw-semibold">{{ estudiante(seguroSeleccionado).name || 'Estudiante' }}</div><small>Documento: {{ estudiante(seguroSeleccionado).documento || '—' }}</small><div class="mt-2">Valor a pagar: <strong>{{ moneda(seguroSeleccionado.valor) }}</strong></div></div>
                    <div class="row g-3"><div class="col-md-6"><label class="form-label">Fecha de pago <span class="text-danger">*</span></label><input v-model="form.fecha_pago" type="date" class="form-control"></div><div class="col-md-6"><label class="form-label">Forma de pago <span class="text-danger">*</span></label><select v-model="form.forma_pago" class="form-select"><option value="">Seleccione...</option><option>Efectivo</option><option>Transferencia</option><option>Consignación</option><option>Tarjeta</option><option>PSE</option></select></div><div class="col-12"><label class="form-label">Referencia</label><input v-model="form.referencia" type="text" class="form-control" placeholder="Número de recibo, comprobante o referencia"></div><div class="col-12"><label class="form-label">Observación</label><textarea v-model="form.observacion" class="form-control" rows="3"></textarea></div></div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end gap-2"><button class="btn btn-outline-secondary" @click="cerrarModal" :disabled="guardando">Cancelar</button><button class="btn btn-success" @click="guardarPago" :disabled="guardando"><i v-if="guardando" class="fa fa-spinner fa-spin me-1"></i><i v-else class="fa fa-check me-1"></i>{{ guardando ? 'Guardando...' : 'Confirmar pago' }}</button></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'SeguroEstudiantil',
    data() {
        return {
            seguros: [], cargando: false, guardando: false, modalRegistro: false, modalPago: false, seguroSeleccionado: null,
            filtros: { buscar: '', ano: '', periodo: '', semestre: '', estado: '' },
            formRegistro: { valor: '', observacion: '' },
            form: { fecha_pago: this.fechaHoy(), forma_pago: '', referencia: '', observacion: '' },
            meta: { current_page: 1, last_page: 1, total: 0 },
        };
    },
    computed: {
        pendientes() { return this.seguros.filter(item => item.seguro_registrado && !Boolean(item.estado)).length; },
        pagados() { return this.seguros.filter(item => item.seguro_registrado && Boolean(item.estado)).length; },
    },
    mounted() { this.cargarSeguros(); },
    methods: {
        fechaHoy() { const f = new Date(); return `${f.getFullYear()}-${String(f.getMonth()+1).padStart(2,'0')}-${String(f.getDate()).padStart(2,'0')}`; },
        cargarSeguros(pagina = 1) {
            this.cargando = true;
            const params = { page: pagina };
            Object.keys(this.filtros).forEach(k => { if (this.filtros[k] !== '' && this.filtros[k] !== null) params[k] = this.filtros[k]; });
            axios.get('/seguro', { params }).then(response => {
                const data = response.data || {};
                this.seguros = data.data || [];
                this.meta = { current_page: data.current_page || 1, last_page: data.last_page || 1, total: data.total || 0 };
            }).catch(error => { this.seguros = []; this.mostrarError(error, 'No fue posible consultar las matrículas.'); }).finally(() => { this.cargando = false; });
        },
        buscar() { this.cargarSeguros(1); },
        limpiarFiltros() { this.filtros = { buscar: '', ano: '', periodo: '', semestre: '', estado: '' }; this.cargarSeguros(1); },
        cambiarPagina(pagina) { if (pagina < 1 || pagina > this.meta.last_page) return; this.cargarSeguros(pagina); },
        abrirRegistro(item) { this.seguroSeleccionado = item; this.formRegistro = { valor: '', observacion: '' }; this.modalRegistro = true; },
        cerrarRegistro() { if (this.guardando) return; this.modalRegistro = false; this.seguroSeleccionado = null; },
        guardarRegistro() {
            if (!this.seguroSeleccionado || this.formRegistro.valor === '' || Number(this.formRegistro.valor) < 0) { this.mostrarError(null, 'Debe indicar un valor válido para el seguro.'); return; }
            this.guardando = true;
            axios.post('/seguro', { asignatura_id: this.seguroSeleccionado.asignatura_id, valor: this.formRegistro.valor, observacion: this.formRegistro.observacion }).then(response => {
                this.mostrarExito(response.data?.message || 'Seguro registrado correctamente.'); this.cerrarRegistro(); this.cargarSeguros(this.meta.current_page);
            }).catch(error => { this.mostrarError(error, error.response?.data?.message || 'No fue posible registrar el seguro.'); }).finally(() => { this.guardando = false; });
        },
        abrirPago(seguro) { this.seguroSeleccionado = seguro; this.form = { fecha_pago: this.fechaHoy(), forma_pago: '', referencia: '', observacion: '' }; this.modalPago = true; },
        cerrarModal() { if (this.guardando) return; this.modalPago = false; this.seguroSeleccionado = null; },
        guardarPago() {
            if (!this.seguroSeleccionado) return;
            if (!this.form.fecha_pago || !this.form.forma_pago) { this.mostrarError(null, 'La fecha y la forma de pago son obligatorias.'); return; }
            if (!window.confirm('¿Confirma que desea registrar el pago único de este seguro?')) return;
            this.guardando = true;
            axios.post(`/seguro/${this.seguroSeleccionado.id}/pagar`, this.form).then(response => { this.mostrarExito(response.data?.message || 'Pago registrado correctamente.'); this.cerrarModal(); this.cargarSeguros(this.meta.current_page); }).catch(error => { this.mostrarError(error, error.response?.data?.message || 'No fue posible registrar el pago.'); }).finally(() => { this.guardando = false; });
        },
        verPago(seguro) { window.alert(`Seguro pagado\n\nFecha: ${seguro.fecha_pago || 'No registrada'}\nForma de pago: ${seguro.forma_pago || 'No registrada'}\nReferencia: ${seguro.referencia || 'Sin referencia'}`); },
        asignatura(seguro) { return seguro?.asignatura || {}; },
        estudiante(seguro) { return this.asignatura(seguro).estudiante || {}; },
        moneda(valor) { return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(Number(valor || 0)); },
        mostrarExito(mensaje) { if (window.toastr) toastr.success(mensaje); else window.alert(mensaje); },
        mostrarError(error, mensaje) { if (window.toastr) toastr.error(mensaje); else window.alert(mensaje); if (error) console.error(error); },
    },
};
</script>

<style scoped>
.seguro-page { padding-bottom: 30px; }
.seguro-icon { width: 46px; height: 46px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 20px; }
.seguro-modal-backdrop { position: fixed; inset: 0; z-index: 2000; background: rgba(0,0,0,.45); display: flex; align-items: center; justify-content: center; padding: 20px; }
.seguro-modal { width: min(720px,100%); max-height: calc(100vh - 40px); overflow-y: auto; }
.table th { white-space: nowrap; }
</style>
