<template>
    <div class="content-wrapper user-admin-page" v-if="seccion == 'Admin'">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl-12">
                    <div class="nav-align-top mb-4 user-tabs">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Registro de Usuarios del Sistema</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Usuarios Registrados</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                <div class="user-section-heading mb-3"><div><h5 class="mb-1"><i class="fa fa-user-plus me-2"></i>Registro de usuarios</h5><p class="mb-0">Crea y configura el acceso de los usuarios al sistema.</p></div></div>
                                <div class="tab-pane show active user-form-panel" id="navs-top-home" role="tabpanel">
                                    <form v-on:submit.prevent="agregar()" method="POST">
                                        <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel" :is-full-page="fullPage" />
                                        <div class="row">
                                            <div class="col-sm-5 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Nombres y Apellidos</label>
                                                <input id="smallInput" class="form-control form-control-sm" :required="name" type="text" name="name" v-model="name" />
                                            </div>
                                            <div class="col-sm-3 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Usuario</label>
                                                <input id="smallInput" class="form-control form-control-sm" :required="usuario" type="text" name="username" v-model="username" />
                                            </div>
                                            <div class="col-md-2 input-group-sm" id="partos">
                                                <label for="smallSelect" class="form-label">Estado</label>
                                                <select class="form-select form-select-sm" v-model="estado">
                                                    <option value="">Seleccione</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 input-group-sm" id="partos">
                                                <label for="smallSelect" class="form-label">Roles</label>
                                                <select class="form-select form-select-sm" v-model="especialidad">
                                                    <option value="">Seleccione</option>
                                                    <option value="Asistentes">Asistentes</option>
                                                    <option value="Admin">Administrador</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Email</label>
                                                <input id="smallInput" class="form-control form-control-sm" :required="email" type="email" name="email" v-model="email" />
                                            </div>
                                            <div class="col-sm-3 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Password</label>
                                                <input class="form-control form-control-sm" :required="password" type="password" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" v-model="password" />
                                            </div>
                                            <div class="col-sm-3 input-group-sm" id="input">
                                                <label for="smallInput" class="form-label">Confirmar</label>
                                                <input class="form-control form-control-sm" :required="password_confirmation" type="password" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" v-model="password_confirmation" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 p-2">
                                                <button type="submit" class="btn btn-sm btn-outline-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                <div class="tab-pane" id="navs-top-profile" role="tabpanel">
                                    <div class="user-section-heading mb-3"><div><h5 class="mb-1"><i class="fa fa-users me-2"></i>Usuarios registrados</h5><p class="mb-0">Consulta los usuarios y administra sus permisos.</p></div></div>
                                    <div class="card border-0 shadow-sm user-table-card">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr><th>Nombres</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Accion</th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="itemm in user.data" :key="itemm.id">
                                                        <th>{{ itemm.name }}</th>
                                                        <th>{{ itemm.username }}</th>
                                                        <th>{{ itemm.especialidad }}</th>
                                                        <th v-if="itemm.estado >= 1"><span class="badge bg-label-primary me-1">Activo</span></th>
                                                        <th v-else><span class="badge bg-label-warning me-1">Inactivo</span></th>
                                                        <th>
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <router-link :to="'/user/' + itemm.id" exact-active-class="active" class="dropdown-item"><a><i class="bx bx-edit-alt me-1"></i> Editar</a></router-link>
                                                                </div>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr v-if="!user.data || user.data.length === 0">
                                                        <td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-12">
                                            <div class="demo-inline-spacing">
                                                <nav aria-label="Page navigation">
                                                    <ul class="pagination pagination-sm">
                                                        <li class="page-item prev" v-if="pagination.current_page > 1">
                                                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)"><i class="tf-icon bx bx-chevrons-left" height="80"></i></a>
                                                        </li>
                                                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="{ active: page === isActived }">
                                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                                        </li>
                                                        <li class="page-item next" v-if="pagination.current_page < pagination.last_page">
                                                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';

let users = document.head.querySelector('meta[name="user"]');
let seccion = users ? JSON.parse(users.content).especialidad : '';

export default {
    components: {
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {
            empresa: {},
            user: { data: [] },
            errors: {},
            seccion: [seccion],
            name: '',
            usuario: '',
            username: '',
            especialidad: '',
            onCancel: this.onCancel,
            email: '',
            estado: '',
            ips_id: '',
            password: '',
            password_confirmation: '',
            role: '',
            isLoading: false,
            fullPage: true,
            pagination: {
                total: 0,
                current_page: 1,
                per_page: 0,
                last_page: 1,
                from: 0,
                to: 0,
            },
            offset: 2,
        };
    },
    computed: {
        isActived() {
            return this.pagination.current_page;
        },
        pagesNumber() {
            if (!this.pagination.to || !this.pagination.last_page) {
                return [];
            }

            let from = this.pagination.current_page - this.offset;
            if (from < 1) from = 1;

            let to = from + (this.offset * 2);
            if (to > this.pagination.last_page) to = this.pagination.last_page;

            const pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    mounted() {
        this.created(1);
    },
    methods: {
        async created(page = 1) {
            this.isLoading = true;

            try {
                const res = await axios.get('/user?page=' + page);
                this.user = res.data.user || { data: [] };
                this.empresa = res.data.empresa || {};
                this.pagination = res.data.pagination || {
                    total: 0,
                    current_page: page,
                    per_page: 0,
                    last_page: 1,
                    from: 0,
                    to: 0,
                };
            } catch (error) {
                this.user = { data: [] };
                toastr.error(error.response?.data?.message || 'No fue posible cargar los usuarios.');
            } finally {
                this.isLoading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.created(page);
        },

        eliminar(id) {
            this.isLoading = true;
            toastr.info('No se puede eliminar el usuario');
            this.isLoading = false;
            this.created(this.pagination.current_page || 1);
        },

        async agregar() {
            this.isLoading = true;

            try {
                await axios.post('/user', {
                    name: this.name,
                    username: this.username,
                    especialidad: this.especialidad,
                    email: this.email,
                    estado: this.estado,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                toastr.success('Se ha guardado exitosamente');

                this.name = '';
                this.username = '';
                this.email = '';
                this.ips_id = '';
                this.password = '';
                this.password_confirmation = '';
                this.especialidad = '';
                this.estado = '';
                this.errors = {};

                // Recargar la lista solamente después de que Laravel confirme el guardado.
                await this.created(1);
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    toastr.error('Verifique los datos ingresados.');
                } else {
                    toastr.error(error.response?.data?.message || 'No fue posible registrar el usuario.');
                }
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>

<style scoped>
.user-admin-page {
    padding: 24px;
    background: #f4f7fb;
    min-height: calc(100vh - 80px);
}
.user-admin-page .container-xxl {
    max-width: 1440px;
    padding-top: 0;
}
.user-admin-page .nav-tabs {
    border-bottom: 1px solid #e7ebf2;
    gap: 6px;
}
.user-admin-page .nav-tabs .nav-link {
    border: 0;
    border-radius: 10px 10px 0 0;
    color: #64748b;
    font-weight: 600;
    padding: 12px 18px;
}
.user-admin-page .nav-tabs .nav-link.active {
    color: #2563eb;
    background: #fff;
    box-shadow: inset 0 3px 0 #2563eb;
}
.user-admin-page .tab-content {
    background: transparent;
    padding-top: 16px;
}
.user-admin-page .tab-pane > .user-section-heading,
.user-admin-page #navs-top-profile > .user-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 12px;
    padding: 18px 22px;
    border: 1px solid #e8edf4;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
}
.user-admin-page .user-section-heading h5 {
    color: #1e293b;
    font-weight: 700;
}
.user-admin-page .user-section-heading p {
    color: #64748b;
    font-size: .875rem;
}
.user-admin-page .user-form-panel,
.user-admin-page .user-table-card {
    background: #fff;
    border: 1px solid #e8edf4 !important;
    border-radius: 12px;
    padding: 22px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .05);
}
.user-admin-page .form-label {
    color: #475569;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .025em;
    margin-bottom: 7px;
}
.user-admin-page .form-control,
.user-admin-page .form-select {
    min-height: 40px;
    border: 1px solid #d9e2ee;
    border-radius: 8px;
    color: #334155;
    box-shadow: none;
}
.user-admin-page .form-control:focus,
.user-admin-page .form-select:focus {
    border-color: #93b4f7;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
}
.user-admin-page .user-form-panel .row {
    row-gap: 12px;
}
.user-admin-page .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 9px 15px;
}
.user-admin-page .btn-outline-success {
    color: #15803d;
    border-color: #86d6a0;
}
.user-admin-page .btn-outline-success:hover {
    color: #fff;
    background: #16a34a;
    border-color: #16a34a;
}
.user-admin-page .user-table-card {
    padding: 0;
    overflow: hidden;
}
.user-admin-page .table-responsive {
    border-radius: 12px;
}
.user-admin-page .table {
    color: #334155;
}
.user-admin-page .table thead th {
    padding: 14px 16px;
    color: #64748b;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .035em;
    white-space: nowrap;
    border-bottom: 1px solid #e8edf4;
}
.user-admin-page .table tbody td,
.user-admin-page .table tbody th {
    padding: 14px 16px;
    font-weight: 500;
    border-color: #edf1f6;
}
.user-admin-page .pagination {
    padding: 16px;
    margin-bottom: 0;
}
.user-admin-page .page-link {
    border-radius: 7px;
    margin: 0 2px;
    color: #2563eb;
}
.user-admin-page .page-item.active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
@media (max-width: 767.98px) {
    .user-admin-page { padding: 12px; }
    .user-admin-page .user-form-panel { padding: 14px; }
    .user-admin-page .nav-tabs .nav-link { padding: 10px 12px; font-size: .85rem; }
    .user-admin-page .user-section-heading { padding: 14px; }
}
</style>
