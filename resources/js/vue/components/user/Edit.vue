<template>
    <div class="card" v-if="seccion == 'Admin'">
        <div class="container-fluid">
            <div class="body">
                <div class="col-sm-12 border mt-2">
                    <label style="background-color: tomato;" class="col-md-12 col-12 border" align="center">Edición de
                        Usuarios</label>
                </div>
                <div class="card-body ">
                    <div class="tab-pane fade show active border mt-2" id="navs-top-home" role="tabpanel">
                        <form v-on:submit.prevent="agregar()" method="POST">

                            <loading v-model:active="isLoading" :can-cancel="true" color="#48FF09" :on-cancel="onCancel"
                                :is-full-page="fullPage" />

                            &nbsp;&nbsp;&nbsp;
                            <div class="col-sm-3 row col-19" id="input">
                                <label for="smallInput" class="form-label">Nombres y Apellidos </label>
                                <input id="smallInput" class="form-control form-control-sm " type="text" name="name"
                                    v-model="user.name" />

                            </div>
                            <div class="col-sm-2 row col-19" id="input">
                                <label for="smallInput" class="form-label">Usuario</label>
                                <input id="smallInput" class="form-control form-control-sm " type="text" name="username"
                                    v-model="user.username" />

                            </div>
                            <div class="col-sm-3 row col-19" id="input">
                                <label for="smallInput" class="form-label" name="username">Email</label>
                                <input id="smallInput" class="form-control form-control-sm" type="email" name="email"
                                    v-model="user.email" />

                            </div>
                            <div class="col-md-2 input-group-sm" id="input">
                                <label for="smallSelect" class="form-label">Estado</label>
                                <select class="form-select form-select-sm" id="" v-model="user.estado">
                                    <option value="">Seleccione</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>

                                </select>
                            </div>

                            <div class="col-md-2 input-group-sm" id="input">
                                
                                <label for="smallSelect" class="form-label">Roles</label>
                                <select class="form-select form-select-sm" id="" v-model="user.especialidad">
                                    <option value="">Seleccione</option>
                                    <option value="Asistentes">Asistentes</option>
                                    <option value="Admin">Administrador</option>

                                </select>
                            </div>

                            <div>

                                <div class="d-flex justify-content-end gap-2 p-2">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar
                                    </button>

                                    <button @click.prevent="volver(e)" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
                                    </button>
                                </div>
                            </div>


                        </form>
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
let users = document.head.querySelector('meta[name="user"]');
let seccion = JSON.parse(users.content).especialidad;
export default {

    data() {
        return {
            id: this.$route.params.id,
            user: {},
            seccion: [seccion],
            name: '',
            username: '',
            estado: '',
            email: '',
            especialidad: '',
            errors: {},
            isLoading: false,
            fullPage: true
        }
    },
    components: {
        Loading,

    },
    created() {

        this.isLoading = true;
        axios.get('/user/' + this.id + '/edit')
            .then(res => {
                this.user = res.data.user;
                //  console.log(this.user)
                this.isLoading = false
            }).catch(error => {
                this.errors = error.response.data.errors;
                this.isLoading = false
            })
    },

    methods: {

        volver() {
            this.$router.push({ path: "/user/index" });
        },
        agregar() {
            this.isLoading = true;
            axios.put('/user/' + this.id, {
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                especialidad: this.user.especialidad,
                estado: this.user.estado,

            })
                .then(response => {
                    toastr.success('Se Ha Actualizado Exitosamente');

                    this.isLoading = false
                    location.reload();
                }).catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                        setTimeout(function () {
                            $(".alert").fadeOut(1500);
                        }, 5000);

                        setTimeout(function () {
                            $(".alert").fadeIn(1500);
                        }, 1000);
                        this.isLoading = false
                    }
                })

        }

    }
}
</script>
