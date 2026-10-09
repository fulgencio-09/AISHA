<template>
    <div align="center" class="container">
        <div class="justify-content-center mt-2">
            <div class="col-md-7 ">
                <div class="card shadow p-3 mb-5 bg-body rounded">
                    <div align="center" class="card-header shadow p-3 mb-2 bg-body rounded">Actualizacion de clave</div>
                    <form v-on:submit.prevent="update()" method="put">                        
                        <div class="card-body border">
                            <div class="form-group ">
                                <label>Password Actual</label>
                                <div  class="col-md-6 ">
                                    <input  align="center" id="mypassword" type="password" class="form-control form-control-sm" name="mypassword"
                                        v-model="mypassword">
                                </div>
                            </div>
                            <div class="form-group">
                                <label >Nuevo Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control form-control-sm"
                                        name="password" autocomplete="password" v-model="password">


                                </div>
                            </div>
                            <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                    :is-full-page="fullPage" />

                            <div class="form-group">
                                <label for="password"> Confirme el Password</label>
                                <div class="col-md-6 ">
                                    <input id="password_confirmation" type="password" class="form-control form-control-sm "
                                        v-model="password_confirmation">


                                </div>
                            </div>


                            <div class="form-group row mb-0">
                                <div   class="col-md-8 offset-md-2 mt-4">
                                    <button  align="center" type="submit"
                                        class="btn btn-outline-success btn-sm shadow  mb-2 ">
                                        Actualizar Contraseña
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
let user = document.head.querySelector('meta[name="user"]')
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
export default {
    components: {
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {
            // id: this.$route.params.id,
            password: '',
            password_confirmation: '',
            mypassword: '',
            

            isLoading: false,
            fullPage: true,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            titulo: '',
            btnedit: false,
            btncrear: false,
            id: '',
            onCancel: this.onCancel,
            btnver: this.btnver,
            datos: '',
            accion: '',

        }
    },
    computed: {
        user() {
            return JSON.parse(user.content);
        },
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
    mounted() {

    },

    methods: {



        

        update: function (e) {
            if (!this.mypassword) {
                toastr.warning('Dijite la contraseña actual es obligatorio');
                return true;
            } else
            if (!this.password) {
                toastr.warning('la nueva contraseña es obligatorio');
                return true;
            } else
            if (!this.password_confirmation) {
                toastr.warning('La confirmacion de la nueva contraseña es obligatorio');
                return true;
            } else
            if (this.password_confirmation != this.password) {
                toastr.warning('No hay coincidencia en la nueva credencial ');
                return true;
            } else
            if (this.password.length < 8) {
                toastr.warning('La contraseña debe tener mas de 8 caracteres ');
                return true;
            } 


            this.isLoading = true;
            axios.put('/cambio/passwords/' + this.user.id, {
                mypassword: this.mypassword,
                password: this.password,
                password_confirmation: this.password_confirmation,                 
            

            })
                .then(res => {
                    this.success = true;
                    if (res.data != 'no') {
                        toastr.success('Se Ha Actualizado Exitosamente');                      
                    } else 
                    if (res.data != 'si') {
                        toastr.warning('La contraseña no pudo ser cambiada' );          
                           }
                           
                           this.mypassword='';
                           this.password_confirmation='';
                           this.password='';
                    this.isLoading = false
                }).catch((error) => {
                  
                })
        }

    }
}





</script>