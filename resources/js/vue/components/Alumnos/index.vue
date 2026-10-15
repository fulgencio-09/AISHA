<template>
    <main class="main estudiantes-page" id="main">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1"><i class="fa fa-graduation-cap me-2"></i>Estudiantes</h4>
                    <p class="text-muted mb-0">Consulta, registra y administra la información de los estudiantes.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" @click="cargarEstudiantes()" :disabled="isLoading"><i class="fa fa-refresh me-1"></i> Actualizar</button>
                    <button type="button" class="btn btn-primary" @click="abrirCrear"><i class="fa fa-plus me-1"></i> Nuevo estudiante</button>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Total estudiantes</small><h3 class="mb-0">{{ pagination.total }}</h3></div><span class="estudiante-icon bg-primary-subtle text-primary"><i class="fa fa-users"></i></span></div></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Activos en la página</small><h3 class="mb-0 text-success">{{ activos }}</h3></div><span class="estudiante-icon bg-success-subtle text-success"><i class="fa fa-check-circle"></i></span></div></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block">Inactivos en la página</small><h3 class="mb-0 text-warning">{{ inactivos }}</h3></div><span class="estudiante-icon bg-warning-subtle text-warning"><i class="fa fa-user-times"></i></span></div></div></div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4"><label class="form-label">Buscar estudiante</label><input v-model.trim="filtros.buscar" @keyup.enter="buscar" type="text" class="form-control" placeholder="Número de documento"></div>
                        <div class="col-md-2"><label class="form-label">Tipo de documento</label><select v-model="filtros.tipo" class="form-select"><option value="">Todos</option><option value="CC">CC</option><option value="TI">TI</option><option value="PT">PT</option><option value="CE">CE</option><option value="AS">AS</option></select></div>
                        <div class="col-md-2"><label class="form-label">Estado</label><select v-model="filtros.estado" class="form-select"><option value="">Todos</option><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
                        <div class="col-md-4 d-flex gap-2"><button type="button" class="btn btn-primary" @click="buscar" :disabled="isLoading"><i class="fa fa-search me-1"></i> Buscar</button><button type="button" class="btn btn-outline-secondary" @click="limpiarFiltros" :disabled="isLoading"><i class="fa fa-eraser me-1"></i> Limpiar</button></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center"><strong>Listado de estudiantes</strong><span class="text-muted small">Total: {{ pagination.total }}</span></div>
                <div v-if="isLoading" class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><div class="mt-2 text-muted">Cargando estudiantes...</div></div>
                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Tipo</th><th>Documento</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Estado</th><th class="text-center">Acciones</th></tr></thead>
                        <tbody>
                            <tr v-for="item in alumno" :key="item.id">
                                <td><span class="document-badge">{{ item.tipo || '—' }}</span></td>
                                <td class="fw-semibold">{{ item.documento || '—' }}</td>
                                <td><div class="fw-semibold">{{ item.name || 'Sin nombre' }}</div><small class="text-muted">Estudiante #{{ item.id }}</small></td>
                                <td>{{ item.telefono || '—' }}</td><td>{{ item.correo || '—' }}</td>
                                <td><span v-if="Number(item.estado) > 0" class="badge bg-success-subtle text-success">Activo</span><span v-else class="badge bg-warning-subtle text-warning">Inactivo</span></td>
                                <td class="text-center"><div class="d-inline-flex gap-1"><button type="button" class="btn btn-sm btn-success" title="Editar" @click="abrirEditar(item)"><i class="fa fa-pencil"></i></button><router-link :to="'/matricula/' + item.id" exact-active-class="active" class="btn btn-sm btn-outline-secondary" title="Ver matrícula"><i class="fa fa-external-link"></i></router-link></div></td>
                            </tr>
                            <tr v-if="!alumno.length"><td colspan="7" class="text-center py-5 text-muted"><i class="fa fa-users fa-2x mb-2 d-block"></i>No hay estudiantes con los filtros seleccionados.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2"><div class="text-muted small">Página {{ pagination.current_page || 1 }} de {{ pagination.last_page || 1 }} · Mostrando {{ registroDesde }}-{{ registroHasta }} de {{ pagination.total || 0 }}</div><nav aria-label="Paginación de estudiantes"><ul class="pagination pagination-sm mb-0"><li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><button class="page-link" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">Anterior</button></li><li v-for="page in pagesNumber" :key="page" class="page-item" :class="{ active: page === pagination.current_page }"><button class="page-link" @click="changePage(page)">{{ page }}</button></li><li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><button class="page-link" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">Siguiente</button></li></ul></nav></div></div>
            </div>
        </div>

        <div v-if="modalFormulario" class="estudiante-modal-backdrop" @click.self="cerrarModal">
            <div class="estudiante-modal card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="fa fa-user me-2"></i>{{ modoEdicion ? 'Editar estudiante' : 'Nuevo estudiante' }}</h5><button type="button" class="btn-close btn-close-white" @click="cerrarModal"></button></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">Tipo de documento <span class="text-danger">*</span></label><select v-model="form.tipo" class="form-select"><option value="">Seleccione</option><option value="CC">CC</option><option value="TI">TI</option><option value="PT">PT</option><option value="CE">CE</option><option value="AS">AS</option></select></div>
                        <div class="col-md-3"><label class="form-label">Documento <span class="text-danger">*</span></label><input v-model.trim="form.documento" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Nombre y apellidos <span class="text-danger">*</span></label><input v-model.trim="form.name" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input v-model.trim="form.correo" type="email" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Dirección <span class="text-danger">*</span></label><input v-model.trim="form.direccion" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Teléfono <span class="text-danger">*</span></label><input v-model.trim="form.telefono" type="text" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label">Estado <span class="text-danger">*</span></label><select v-model="form.estado" class="form-select"><option value="">Seleccione</option><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end gap-2"><button type="button" class="btn btn-outline-secondary" @click="cerrarModal" :disabled="guardando">Cancelar</button><button v-if="!modoEdicion" type="button" class="btn btn-primary" @click="guardar" :disabled="guardando"><i v-if="guardando" class="fa fa-spinner fa-spin me-1"></i><i v-else class="fa fa-check me-1"></i>{{ guardando ? 'Guardando...' : 'Guardar' }}</button><button v-else type="button" class="btn btn-success" @click="actualizar" :disabled="guardando"><i v-if="guardando" class="fa fa-spinner fa-spin me-1"></i><i v-else class="fa fa-refresh me-1"></i>{{ guardando ? 'Actualizando...' : 'Actualizar' }}</button></div>
            </div>
        </div>

        <loading v-model:active="isLoading" color="#48FF09" :can-cancel="false" :is-full-page="fullPage" />
    </main>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
    name: 'Estudiantes',
    components: { Loading },
    data() {
        return { alumno: [], isLoading: false, guardando: false, fullPage: false, modalFormulario: false, modoEdicion: false, id: null, filtros: { buscar: '', tipo: '', estado: '' }, form: this.formularioVacio(), pagination: { total: 0, current_page: 1, per_page: 5, last_page: 1, from: 0, to: 0 }, offset: 2 };
    },
    computed: {
        activos() { return this.alumno.filter(item => Number(item.estado) > 0).length; },
        inactivos() { return this.alumno.filter(item => Number(item.estado) === 0).length; },
        registroDesde() { return this.pagination.total === 0 ? 0 : (this.pagination.from || ((this.pagination.current_page - 1) * this.pagination.per_page + 1)); },
        registroHasta() { return this.pagination.to || 0; },
        pagesNumber() { const total = this.pagination.last_page || 1; const actual = this.pagination.current_page || 1; const inicio = Math.max(1, Math.min(actual - this.offset, Math.max(1, total - (this.offset * 2)))); const fin = Math.min(total, inicio + (this.offset * 2)); const paginas = []; for (let i = inicio; i <= fin; i++) paginas.push(i); return paginas; },
    },
    mounted() { this.cargarEstudiantes(); },
    methods: {
        formularioVacio() { return { tipo: '', documento: '', name: '', direccion: '', telefono: '', correo: '', estado: '' }; },
        mostrarError(error, fallback) { const mensaje = error?.response?.data?.message || error?.response?.data?.error || fallback; if (window.toastr) toastr.error(mensaje); },
        cargarEstudiantes(page = 1) { this.isLoading = true; const params = { page }; if (this.filtros.buscar) params.documento = this.filtros.buscar; axios.get('/alumno', { params }).then(res => { this.alumno = res.data?.alumno?.data || []; this.pagination = res.data?.pagination || this.pagination; if (!this.pagination.current_page) this.pagination.current_page = page; }).catch(error => { this.alumno = []; this.mostrarError(error, 'No fue posible consultar los estudiantes.'); }).finally(() => { this.isLoading = false; }); },
        buscar() { if (!this.filtros.buscar) { this.cargarEstudiantes(1); return; } this.isLoading = true; axios.post('/alumno/buscar', { documento: this.filtros.buscar }).then(res => { this.alumno = res.data?.alumno?.data || []; this.pagination = res.data?.pagination || this.pagination; }).catch(error => { this.alumno = []; this.mostrarError(error, 'No fue posible realizar la búsqueda.'); }).finally(() => { this.isLoading = false; }); },
        limpiarFiltros() { this.filtros = { buscar: '', tipo: '', estado: '' }; this.cargarEstudiantes(1); },
        changePage(page) { if (page < 1 || page > this.pagination.last_page) return; if (this.filtros.buscar) { this.isLoading = true; axios.post('/alumno/buscar?page=' + page, { documento: this.filtros.buscar }).then(res => { this.alumno = res.data?.alumno?.data || []; this.pagination = res.data?.pagination || this.pagination; }).catch(error => this.mostrarError(error, 'No fue posible cambiar de página.')).finally(() => { this.isLoading = false; }); } else this.cargarEstudiantes(page); },
        abrirCrear() { this.modoEdicion = false; this.id = null; this.form = this.formularioVacio(); this.modalFormulario = true; },
        abrirEditar(item) { this.modoEdicion = true; this.id = item.id; this.form = { tipo: item.tipo || '', documento: item.documento || '', name: item.name || '', direccion: item.direccion || '', telefono: item.telefono || '', correo: item.correo || '', estado: String(item.estado ?? '') }; this.modalFormulario = true; },
        cerrarModal() { if (this.guardando) return; this.modalFormulario = false; this.form = this.formularioVacio(); this.id = null; },
        validar() { if (!this.form.tipo) return this.aviso('El tipo de documento es obligatorio.'); if (!this.form.documento) return this.aviso('El documento es obligatorio.'); if (!this.form.name) return this.aviso('El nombre es obligatorio.'); if (!this.form.direccion) return this.aviso('La dirección es obligatoria.'); if (!this.form.telefono) return this.aviso('El teléfono es obligatorio.'); if (!this.form.correo) return this.aviso('El email es obligatorio.'); if (this.form.estado === '') return this.aviso('El estado del estudiante es obligatorio.'); return true; },
        aviso(mensaje) { if (window.toastr) toastr.warning(mensaje); return false; },
        payload() { return { tipo: this.form.tipo, name: this.form.name, documento: this.form.documento, direccion: this.form.direccion, telefono: this.form.telefono, correo: this.form.correo, estado: this.form.estado }; },
        guardar() { if (!this.validar()) return; this.guardando = true; axios.post('/alumno', this.payload()).then(res => { if (res.data === 'no') { if (window.toastr) toastr.warning('Este estudiante ya está registrado.'); return; } if (window.toastr) toastr.success('Estudiante guardado exitosamente.'); this.cerrarModal(); this.cargarEstudiantes(1); }).catch(error => this.mostrarError(error, 'No fue posible guardar el estudiante.')).finally(() => { this.guardando = false; }); },
        actualizar() { if (!this.validar() || !this.id) return; this.guardando = true; axios.put('/alumno/' + this.id, this.payload()).then(() => { if (window.toastr) toastr.success('Estudiante actualizado exitosamente.'); this.cerrarModal(); this.cargarEstudiantes(this.pagination.current_page || 1); }).catch(error => this.mostrarError(error, 'No fue posible actualizar el estudiante.')).finally(() => { this.guardando = false; }); },
    },
};
</script>

<style scoped>
.estudiantes-page { padding-bottom: 35px; }
.estudiante-icon { width: 46px; height: 46px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; }
.document-badge { display: inline-block; min-width: 34px; text-align: center; padding: 4px 8px; border-radius: 6px; background: #eef2ff; color: #5661f2; font-size: 12px; font-weight: 600; }
.estudiantes-page .card { border-radius: 10px; }
.estudiantes-page .card-header { min-height: 54px; }
.estudiantes-page .table th { font-size: 12px; text-transform: uppercase; letter-spacing: .4px; color: #68758a; white-space: nowrap; }
.estudiantes-page .table td { font-size: 13px; color: #596579; }
.estudiantes-page .form-label { font-size: 12px; font-weight: 600; color: #68758a; }
.estudiantes-page .pagination .page-link { min-width: 36px; text-align: center; }
.estudiante-modal-backdrop { position: fixed; inset: 0; z-index: 1080; background: rgba(28, 40, 54, .55); display: flex; align-items: center; justify-content: center; padding: 20px; overflow-y: auto; }
.estudiante-modal { width: min(900px, 100%); border-radius: 12px; max-height: calc(100vh - 40px); overflow: hidden; }
.estudiante-modal .card-body { overflow-y: auto; }
.estudiante-modal .card-header { min-height: 58px; }
@media (max-width: 767px) { .estudiantes-page .d-flex.gap-2 { width: 100%; margin-top: 12px; } .estudiantes-page .d-flex.gap-2 .btn { flex: 1; } .estudiante-modal-backdrop { padding: 10px; } }
</style>
