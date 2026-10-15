<template>
    <main class="main notas-page" id="main">
        <div class="container-fluid py-3">
            <section class="notas-card" id="listado" v-show="vista === 'listado'">
                <div class="section-title">Matriculados</div>
                <div class="table-responsive">
                    <table class="table notas-table mb-0">
                        <thead>
                            <tr>
                                <th>DOCUMENTO</th><th>NOMBRE</th><th>SEMESTRE</th><th>ACCIONES</th>
                            </tr>
                            <tr class="filter-row">
                                <th><input type="search" id="searchbox" class="form-control form-control-sm" v-model="identidad" @input="buscar" placeholder="Documento"></th>
                                <th></th><th></th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="items in nota" :key="items.id">
                                <td>{{ items.documento }}</td>
                                <td class="text-uppercase">{{ items.name }}</td>
                                <td>{{ items.semestre }}</td>
                                <td class="actions-cell">
                                    <button @click="consulta(items)" class="btn btn-secondary btn-sm action-btn" title="Certificado de notas"><i class="fa fa-print"></i></button>
                                    <button v-if="!tieneMaterias(items)" @click="materia(items)" class="btn btn-info btn-sm action-btn" title="Agregar Materia"><i class="fa fa-file-text" aria-hidden="true"></i></button>
                                    <button @click="getNotas(items)" class="btn btn-success btn-sm action-btn" title="Agregar Notas"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <tr v-if="!nota.length"><td colspan="4" class="empty-row">No se encontraron estudiantes matriculados.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="total-label">Total: <strong>{{ pagination.total || 0 }}</strong></div>
                    <nav v-if="pagesNumber.length" aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">«</a></li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="{ active: page === isActived }"><a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a></li>
                            <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">»</a></li>
                        </ul>
                    </nav>
                </div>
            </section>

            <section class="notas-card gestion-card" id="dos" v-show="vista === 'gestion'">
                <div class="section-title">Gestión de Notas</div>
                <form @submit.prevent="guardarNota">
                    <div class="form-panel">
                        <div class="row g-3">
                            <div class="col-12 col-md-4"><label class="field-label">MATERIA</label><input type="text" v-model="form.materia" class="form-control" disabled></div>
                            <div class="col-12 col-md-2"><label class="field-label">PRIMER CORTE</label><input type="number" step="0.1" min="0" max="5" v-model="form.corte1" class="form-control"></div>
                            <div class="col-12 col-md-2"><label class="field-label">SEGUNDO CORTE</label><input type="number" step="0.1" min="0" max="5" v-model="form.corte2" class="form-control"></div>
                            <div class="col-12 col-md-2"><label class="field-label">TERCER CORTE</label><input type="number" step="0.1" min="0" max="5" v-model="form.corte3" class="form-control"></div>
                            <div class="col-12 col-md-2 d-flex align-items-end"><button type="submit" v-if="editando" class="btn btn-outline-success w-100"><i class="fa fa-refresh me-1"></i> Actualizar</button></div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive mt-4">
                    <table class="table notas-table mb-0">
                        <thead><tr><th>SEMESTRE</th><th>MATERIA</th><th>CORTE 1</th><th>CORTE 2</th><th>CORTE 3</th><th>ACUMULADO</th><th>ACCIONES</th></tr></thead>
                        <tbody>
                            <tr v-for="nota in notas" :key="nota.id">
                                <td>{{ nota.semestre }}</td><td class="text-uppercase">{{ nota.materia }}</td><td>{{ nota.corte1 ?? '-' }}</td><td>{{ nota.corte2 ?? '-' }}</td><td>{{ nota.corte3 ?? '-' }}</td><td>{{ calcularAcumulado(nota) }}</td>
                                <td class="actions-cell"><button @click="editarNota(nota)" class="btn btn-warning btn-sm action-btn" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                            </tr>
                            <tr v-if="!notas.length"><td colspan="7" class="empty-row">Seleccione un estudiante para consultar sus notas.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-actions">
                    <button v-if="editando" type="button" @click="cancelarEdicion" class="btn btn-outline-secondary"><i class="bx bx-window-close me-1"></i> Cancelar</button>
                    <button @click="ocultar" type="button" class="btn btn-warning"><i class="bx bx-log-out me-1"></i> Salir</button>
                </div>
            </section>
            <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel" :is-full-page="true" />
        </div>
    </main>
</template>

<script>
import axios from 'axios'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';

export default {
    components: { Loading, 'Pagination': LaravelVuePagination },
    computed: {
        isActived() { return this.pagination.current_page; },
        pagesNumber() {
            if (!this.pagination.to) return [];
            let from = this.pagination.current_page - this.offset;
            if (from < 1) from = 1;
            let to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) to = this.pagination.last_page;
            const pagesArray = [];
            while (from <= to) { pagesArray.push(from); from++; }
            return pagesArray;
        }
    },
    data() {
        return {
            salir: false, isLoading: false, activeClass: 'text-success', errorClass: 'text-danger', onCancel: this.onCancel,
            identidad: '', vista: 'listado', asignaturaActual: null, estudianteActual: null,
            pagination: { total: 0, current_page: 0, per_page: 0, last_page: 0, from: 0, to: 0 },
            offset: 2, nota: [], notas: [],
            form: { semestre: '', materia: '', materia_id: null, asignatura_id: null, corte1: null, corte2: null, corte3: null, habilitada_recuperacion: false },
            editando: false, idEdit: null, matriculas: [],
        }
    },
    mounted() { this.created(1); },
    methods: {
        changePage(page) { if (page < 1 || page > this.pagination.last_page) return; this.pagination.current_page = page; this.created(page); },
        tieneMaterias(items) { return String(items?.mat_matr ?? '').trim().toLowerCase() === 'si'; },
        calcularAcumulado(nota) {
            const c1 = Number(nota.corte1), c2 = Number(nota.corte2), c3 = Number(nota.corte3);
            if ([nota.corte1, nota.corte2, nota.corte3].some(v => v === null || v === undefined || v === '')) return '-';
            return (c1 * 0.35 + c2 * 0.30 + c3 * 0.35).toFixed(2);
        },
        imprimirTabla() { window.print(); },
        async consulta(items) {
            this.isLoading = true;
            try {
                const res = await axios.get(`/notas/${items.asignatura_id}`);
                this.notas = Array.isArray(res.data) ? res.data : [];
                this.imprimirCertificado(items, this.notas);
            } catch (error) {
                console.error(error);
                if (window.toastr) toastr.error(error.response?.data?.message || 'No fue posible consultar las notas.');
            } finally { this.isLoading = false; }
        },
        escaparHtml(valor) {
            return String(valor ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\"/g, '&quot;').replace(/'/g, '&#039;');
        },
        nombreSemestre(semestre) {
            const nombres = { 1: 'Primer', 2: 'Segundo', 3: 'Tercer', 4: 'Cuarto', 5: 'Quinto' };
            return nombres[Number(semestre)] || `${semestre}°`;
        },
        imprimirCertificado(items, notas) {
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            const semestre = this.nombreSemestre(items?.semestre);
            const estudiante = this.escaparHtml(items?.name || ''), documento = this.escaparHtml(items?.documento || ''), anio = this.escaparHtml(items?.año || '');
            const fecha = new Date().toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' });
            const filas = (Array.isArray(notas) ? notas : []).map(nota => `
                <tr><td>${this.escaparHtml(nota.materia)}</td><td>${nota.corte1 ?? '-'}</td><td>${nota.corte2 ?? '-'}</td><td>${nota.corte3 ?? '-'}</td><td>${this.calcularAcumulado(nota)}</td></tr>
            `).join('');
            const acumulados = (Array.isArray(notas) ? notas : []).map(n => this.calcularAcumulado(n)).filter(v => v !== '-');
            const promedio = acumulados.length ? (acumulados.reduce((sum, valor) => sum + Number(valor), 0) / acumulados.length).toFixed(2) : '-';
            const contenido = `
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificación de Notas - ${estudiante}</title>
<style>
@page { size: A4; margin: 20mm 18mm; } * { box-sizing: border-box; } body { font-family: Arial, Helvetica, sans-serif; color: #222; margin: 0; font-size: 11px; }
.certificado { max-width: 780px; margin: 0 auto; } .encabezado { text-align: center; border-bottom: 2px solid #4b6b8a; padding-bottom: 12px; }
.logo { max-height: 75px; max-width: 150px; margin-bottom: 5px; } .institucion { color: #4b6b8a; font-size: 14px; font-weight: bold; text-transform: uppercase; } .nit { margin-top: 4px; font-size: 10px; }
.titulo { text-align: center; margin: 24px 0 18px; font-size: 18px; font-weight: bold; text-transform: uppercase; } .texto { line-height: 1.7; text-align: justify; margin-bottom: 15px; }
.datos { width: 100%; border-collapse: collapse; margin: 12px 0 18px; } .datos td { padding: 6px 8px; border: 1px solid #d6dde5; } .datos .label { width: 18%; background: #f2f5f8; font-weight: bold; }
.notas { width: 100%; border-collapse: collapse; margin-top: 12px; } .notas th { background: #eef2f6; font-weight: bold; text-transform: uppercase; } .notas th, .notas td { border: 1px solid #aeb8c2; padding: 7px; text-align: center; } .notas th:first-child, .notas td:first-child { text-align: left; }
.promedio { text-align: right; margin-top: 12px; font-weight: bold; } .firma { margin-top: 65px; width: 260px; text-align: center; } .linea { border-top: 1px solid #222; margin-bottom: 7px; } .pie { margin-top: 35px; text-align: center; color: #666; font-size: 9px; }
</style>
</head>
<body>
<div class="certificado">
<div class="encabezado"><img src="${logoUrl}" class="logo" alt="AISHA"><div class="institucion">Fundación para el Desarrollo Educativo y Social de La Guajira</div><div class="nit"><strong>NIT:</strong> 901691754-7</div></div>
<div class="titulo">Certificación de Notas</div>
<div class="texto">La Fundación para el Desarrollo Educativo y Social de La Guajira certifica que el(la) estudiante <strong>${estudiante}</strong>, identificado(a) con documento No. <strong>${documento}</strong>, cursó las asignaturas correspondientes al <strong>${semestre} semestre</strong>${anio ? ` del año <strong>${anio}</strong>` : ''}, de acuerdo con los registros académicos disponibles en el sistema institucional.</div>
<table class="datos"><tr><td class="label">Estudiante</td><td>${estudiante}</td></tr><tr><td class="label">Documento</td><td>${documento}</td></tr><tr><td class="label">Semestre</td><td>${semestre}</td></tr>${anio ? `<tr><td class="label">Año</td><td>${anio}</td></tr>` : ''}<tr><td class="label">Fecha de expedición</td><td>${fecha}</td></tr></table>
<table class="notas"><thead><tr><th>Materia</th><th>Corte 1</th><th>Corte 2</th><th>Corte 3</th><th>Nota final</th></tr></thead><tbody>${filas || '<tr><td colspan="5">No hay calificaciones registradas para este semestre.</td></tr>'}</tbody></table>
<div class="promedio">Promedio del semestre: ${promedio}</div>
<div class="texto" style="margin-top: 22px;">La presente certificación se expide a solicitud del interesado para los fines que estime convenientes.</div>
<div class="firma"><div class="linea"></div><strong>Área Académica</strong><br>Fundación para el Desarrollo Educativo y Social de La Guajira</div>
<div class="pie">Documento generado por el sistema académico AISHA.</div>
</div>
<script>
window.onload = function () { setTimeout(function () { window.focus(); window.print(); window.close(); }, 500); };
<\/script>
</body>
</html>`;
            const ventana = window.open('', '_blank');
            if (!ventana) { if (window.toastr) toastr.warning('El navegador bloqueó la ventana de impresión. Permita las ventanas emergentes para este sitio.'); return; }
            ventana.document.open(); ventana.document.write(contenido); ventana.document.close();
        },
        buscar() {
            this.isLoading = true;
            axios.post('/notas/buscar', { identidad: this.identidad }).then((res) => { this.nota = res.data.nota.data; this.pagination = res.data.pagination; }).catch((error) => { console.error(error); this.nota = []; }).finally(() => { this.isLoading = false; });
        },
        async created(page = 1) {
            this.vista = 'listado'; this.isLoading = true;
            try { const res = await axios.get('/nota?page=' + page); this.nota = res.data.nota.data; this.pagination = res.data.pagination; }
            catch (error) { console.error('No fue posible cargar los matriculados:', error); this.nota = []; if (window.toastr) toastr.error('No fue posible cargar los matriculados.'); }
            finally { this.isLoading = false; }
        },
        ocultar() { this.vista = 'listado'; this.editando = false; this.idEdit = null; this.created(this.pagination.current_page || 1); },
        async materia(items) {
            this.isLoading = true;
            try {
                const response = await axios.post('/agregar/materia', { semestre: items.semestre, matricula: items.asignatura_id });
                items.mat_matr = 'Si'; if (window.toastr) toastr.success(response.data?.message || 'Materias agregadas correctamente.'); await this.getNotas(items);
            } catch (error) { console.error('No fue posible agregar las materias:', error); const mensaje = error.response?.data?.message || 'No fue posible agregar las materias del semestre.'; if (window.toastr) toastr.error(mensaje); else window.alert(mensaje); }
            finally { this.isLoading = false; }
        },
        async getNotas(items) {
            if (!items?.asignatura_id) return;
            this.salir = false; this.idEdit = null; this.editando = false; this.asignaturaActual = items.asignatura_id; this.estudianteActual = items; this.vista = 'gestion';
            try {
                const res = await axios.get(`/notas/${items.asignatura_id}`); this.notas = Array.isArray(res.data) ? res.data : [];
                this.form = { semestre: items.semestre, materia: '', materia_id: null, asignatura_id: items.asignatura_id, corte1: null, corte2: null, corte3: null, habilitada_recuperacion: false };
            } catch (error) { console.error('No fue posible consultar las notas:', error); this.notas = []; if (window.toastr) toastr.error(error.response?.data?.message || 'No fue posible cargar las notas del estudiante.'); }
        },
        async guardarNota() {
            if (!this.idEdit) return; this.isLoading = true;
            try {
                await axios.put(`/nota/${this.idEdit}`, { corte1: this.form.corte1, corte2: this.form.corte2, corte3: this.form.corte3, asignatura_id: this.asignaturaActual });
                if (window.toastr) toastr.success('Calificación actualizada correctamente.'); this.editando = false; this.idEdit = null; await this.getNotas(this.estudianteActual);
            } catch (error) { console.error(error); if (window.toastr) toastr.error(error.response?.data?.message || 'No fue posible actualizar la calificación.'); }
            finally { this.isLoading = false; }
        },
        editarNota(nota) {
            this.idEdit = nota.id; this.editando = true;
            this.form = { ...this.form, semestre: nota.semestre, materia: nota.materia, materia_id: nota.materia_id, asignatura_id: nota.asignatura_id, corte1: nota.corte1, corte2: nota.corte2, corte3: nota.corte3, habilitada_recuperacion: nota.habilitada_recuperacion };
        },
        cancelarEdicion() { this.idEdit = null; this.editando = false; this.form = { ...this.form, materia: '', materia_id: null, corte1: null, corte2: null, corte3: null }; },
    }
}
</script>

<style scoped>
.notas-page { min-height: 100vh; }
.notas-card { background: #fff; border: 1px solid #e4e9ef; border-radius: 10px; box-shadow: 0 4px 14px rgba(31, 45, 61, .08); padding: 16px; margin-bottom: 18px; }
.section-title { border: 1px solid #d5dce4; color: #536b84; text-align: center; text-transform: none; font-size: 15px; padding: 5px 10px; margin-bottom: 16px; background: #fff; }
.notas-table { border: 1px solid #d5dce4; font-size: 13px; }
.notas-table th { color: #536b84; font-size: 12px; letter-spacing: .7px; text-align: center; vertical-align: middle; border-color: #d5dce4; padding: 10px 8px; }
.notas-table td { text-align: center; vertical-align: middle; border-color: #d5dce4; padding: 9px 8px; }
.filter-row th { padding: 8px; } .filter-row input { max-width: 180px; } .actions-cell { white-space: nowrap; }
.action-btn { min-width: 36px; height: 32px; margin: 0 2px; border-radius: 6px; box-shadow: 0 3px 7px rgba(31,45,61,.16); }
.table-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; gap: 12px; } .total-label { color: #62758a; font-size: 13px; }
.pagination .page-link { color: #61758c; border-color: #e2e7ec; } .pagination .page-item.active .page-link { color: #fff; background: #6c63ff; border-color: #6c63ff; }
.form-panel { border: 1px solid #d5dce4; padding: 14px; background: #fff; } .field-label { display: block; color: #536b84; font-size: 12px; margin-bottom: 6px; }
.form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; } .empty-row { color: #7d8b99; padding: 25px !important; }
@media (max-width: 768px) { .table-footer { flex-direction: column; align-items: flex-start; } .notas-table { min-width: 760px; } }
</style>
