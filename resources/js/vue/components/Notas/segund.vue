

<template>
    <form class="col-14 col-md-12" v-on:submit.prevent="agregar()" method="post">
        <div class="table-responsive  ">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Materias</th>
                        <th>Calificacion</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            psicologia del desarrollo:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="i" class="form-control" required type="text" v-model="psicologia">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la educacion fisica:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="j" class="form-control" required type="text" v-model="deporte">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la matematica:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="k" class="form-control" required type="text" v-model="matematica">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la lengua castellana:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="l" class="form-control" required type="text" v-model="castellano">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la ciencias sociales:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="ll" class="form-control" required type="text" v-model="sociales">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la educacion artistica
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="m" class="form-control" required type="text" v-model="artistica">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            didactica de la ciencias naturales
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="n" class="form-control" required type="text" v-model="naturales">
                            </div>
                        </td>

                    </tr>
                </thead>

                <thead>
                    <tr>
                        <th>
                            modelos educativos flexibles MEF:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="ñ" class="form-control" required type="text" v-model="mef">
                            </div>
                        </td>

                    </tr>
                </thead>

                <thead>
                    <tr>
                        <th>
                            practicas pedagogica e investigativas:
                        </th>
                        <td>
                            <div class="input-group-sm ">
                                <input id="o" class="form-control" required type="text" v-model="practicas">
                            </div>
                        </td>

                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                            Corte:
                        </th>
                        <td>
                            <div class="input-group-sm">                              
                                <select class="form-select form-select-sm" id="c3" v-model="corte">
                                    <option value="">Seleccione</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>

                                </select>
                            </div>
                        </td>

                    </tr>
                </thead>



            </table>
            <loading v-model:active="isLoading" :can-cancel="true" color="#48FF09" :on-cancel="onCancel"
                                :is-full-page="fullPage" />
        </div>
        <hr>
        <br>
        <div class="modal-footer p-0">
            <button type="submit" class="btn-sm btn-outline-success "><i class="fa fa-check edu-checked-pro"
                    aria-hidden="true"></i> Guardar</button>
            <button @click.prevent="created()" type="button" class="btn-sm btn-outline-danger"> <i
                    class='bx bx-window-close bx-tada-hover'></i>
                Cancelar</button>


        </div>
    </form>
</template>
<script>
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
import Loading from 'vue-loading-overlay';
export default {
    name: 'segundo',
    props: [
        "semestre_id",
        "estudiante_id",
        "asignatura"

    ],
    components: {
        Loading,
        'Pagination': LaravelVuePagination,



    },
    data() {
        return {
            isLoading: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            onCancel: this.onCancel,       
            fullPage: '',
            psicologia: '',
            deporte: '',
            matematica: '',
            corte:'',
            sociales: '',
            castellano: '',
            artistica: '',
            naturales: '',
            practicas: '',
            mef: '',
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'last_page': 0,
                'to': 0,

            },
            offset: 2,
        }

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
    methods: {
        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },
        async created(page) {
            segundos.hidden = true;
            this.isLoading = true;
            axios.get('/nota?page=' + page)
                .then(res => {

                    this.nota = res.data.nota.data;
                    this.pagination = res.data.pagination
                    this.isLoading = false
                    this.año = '',
                        this.matematica = '',
                        this.sociales = '',
                        this.naturales = '',
                        this.deporte = '',
                        this.artistica = '',
                        this.corte='',
                        this.castellano = '',
                        this.practicas = '',
                        this.mef = '',
                        this.dibujo = '',
                        this.psicologia = '',
                        this.estado = ''
                    this.btncrear = true;
                    this.btnedit = false;
                    $('#staticBackdrop').modal('hide');
                    //$('#modalCenter').modal('show');
                }).catch(error => {

                    // toastr.warning('sesion caducada');
                    //location.reload()
                    this.isLoading = false
                    return true;

                })

        },
        agregar() {
            //    console.log(this.psicologia)

            this.isLoading = true;
            axios.post('/nota', {
                estudiante_id: this.estudiante_id,
                semestre: this.semestre_id,
                matematica: this.matematica,
                mef: this.mef,
                naturales: this.naturales,
                sociales: this.sociales,
                artistica: this.artistica,
                deporte: this.deporte,
                practicas: this.practicas,
                castellano: this.castellano,
                psicologia: this.psicologia,
                estado: this.estado = 1,
                corte:this.corte,
                asignatura_id:this.asignatura
            })
                .then(res => {
                    this.success = true;
                    this.created();
                    if (res.data != 'no') {
                        toastr.success('Se Ha Guardado Exitosamente');
                    }else{
                        toastr.warning('Las notas para este corte ya fueron subida');
                    }
                    this.name = '';
                    this.estado = '';
                    this.isLoading = false

                }).catch((error) => {
                    toastr.warning('sesion caducada');
                    location.reload()
                    this.isLoading = false
                    return true;
                })


        },


    }


}


</script>