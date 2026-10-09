<!-- resources/js/components/NotasCrud.vue -->
<template>
    <main class="main" id="main">
        <div class="container-fluid " id="listado">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h6 align="center" class=" border p-1 col-md-12">Matriculados </h6>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered col-10">
                                <thead>
                                    <tr>
                                        <th>documento</th>
                                        <th>Nombre</th>
                                        <th>Semestre</th>
                                        <th>Acciones</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="input-group-sm ">
                                                <input type="search" id="searchbox" class="form-control"
                                                    v-model="identidad" @input="buscar()">
                                            </div>
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="items in nota" :key="items.id">
                                        <td>{{ items.documento }}</td>
                                        <td>{{ items.name }}</td>
                                        <td>{{ items.semestre }}</td>
                                        <td><button @click="consulta(items)" style="margin-right: 5px;"
                                                class="btn btn-secondary btn-sm" title="Imprimir">
                                                <i class="fa fa-print"></i>
                                            </button>
                                            <button style="margin-right: 5px;" v-if="items.mat_matr === 'No'"
                                                @click="materia(items)" class="btn btn-info btn-sm"
                                                title="Agregar Materia">
                                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                            </button>
                                            <button @click="getNotas(items)" class="btn btn-success btn-sm"
                                                title="Agregar Notas">
                                                <i class="fa fa-clipboard" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-12">
                                <div class="demo-inline-spacing">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm">
                                            <li class="page-item prev" v-if="pagination.current_page > 1">
                                                <a class="page-link" href="#"
                                                    @click.prevent="changePage(pagination.current_page - 1)">
                                                    <i class="tf-icon bx bx-chevrons-left" height="80"></i>
                                                </a>
                                            </li>
                                            <li class="page-item" v-for="page in pagesNumber " :key="page"
                                                v-bind:class="[page === isActived ? 'active' : '']">
                                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page
                                                    }}</a>
                                            </li>

                                            <li class="page-item next"
                                                v-if="pagination.current_page < pagination.last_page">
                                                <a class="page-link" href="#"
                                                    @click.prevent="changePage(pagination.current_page + 1)">
                                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                                </a>
                                            </li>

                                        </ul>

                                    </nav>

                                    <!--/ Basic Pagination -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- antes yabña  -->

    <div class="card col-md-12 col-12" id="dos">
        <div class="card-body">
            <h6 align="center" class=" border p-1 col-md-12">Gestion de Notas</h6>
            <form @submit.prevent="guardarNota">
                <div class="row border p-1">
                    <div class="col-md-4 col-12">
                        <h6 align="center" class="col-md-12"><label for="nameWithTitle"
                                class="form-label">materia</label></h6>
                        <input type="text" v-model="form.materia" class="form-control form-control-sm" disabled
                            required>
                    </div>
                    <div class="col-md-2">
                        <h6 align="center" class="col-md-12"><label for="nameWithTitle" class="form-label">Primer
                                Corte</label></h6>
                        <input type="number" step="0.1" min="0" max="5" v-model="form.corte1"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <h6 align="center" class="col-md-12"><label for="nameWithTitle" class="form-label">Segundo
                                Corte</label></h6>
                        <input type="number" step="0.1" min="0" max="5" v-model="form.corte2"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <h6 align="center" class="col-md-12"><label for="nameWithTitle" class="form-label">Tercer
                                Corte</label></h6>
                        <input type="number" step="0.1" min="0" max="5" v-model="form.corte3"
                            class="form-control form-control-sm">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2">

                    <button type="submit" v-if="editando" class="btn btn-outline-success btn-sm mt-3"> <i
                            class="fa fa-refresh"></i> Actualizar
                    </button>
                    <button type="button" v-if="editando" @click="cancelarEdicion"
                        class="btn btn-outline-secondary mt-3 btn-sm ms-2"><i
                            class="bx bx-window-close bx-tada-hover"></i> Cancelar</button>
                </div>
            </form>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered mt-4 ">
                    <thead>
                        <tr>

                            <th>Semestre</th>
                            <th>Materia</th>
                            <th>Corte 1</th>
                            <th>Corte 2</th>
                            <th>Corte 3</th>
                            <th>Acumulado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="nota in notas" :key="nota.id">

                            <td>{{ nota.semestre }}</td>
                            <td>{{ nota.materia }}</td>
                            <td>{{ nota.corte1 ?? '-' }}</td>
                            <td>{{ nota.corte2 ?? '-' }}</td>
                            <td>{{ nota.corte3 ?? '-' }}</td>
                            <td>{{ (nota.corte3 * .35 + nota.corte1 * .35 + nota.corte2 * .30) ?? '-' }} </td>
                            <td>
                                <button @click="editarNota(nota)" class="btn btn-sm btn-warning me-1"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="d-flex justify-content-end gap-2">
                <button @click="ocultar()" class="btn btn-outline-danger btn-sm mt-3"> <i
                        class="bx bx-window-close bx-tada-hover"></i> Salir
                </button>
            </div>
        </div>
    </div>
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
            identidad:'',
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
            window.print(table); // imprime toda la página, incluida la tabla
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
            } else
                if (notas.semestre == 2) {
                    var sem = "Segundo";
                } else
                    if (notas.semestre == 3) {
                        var sem = "Tercer";
                    } else
                        if (notas.semestre == 4) {
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
th { border-bottom: 1px solid black;  }
th { border-top: 1px solid black;  } 
#ti {  text-align: center;  }
#im {   text-align: right;  }
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
<tr>
<td></td>
<th >Total:</th>

</tr>
</tfoot>
<tfoot>
<tr>
<td ></td>
<td >Sub Total:</td>
<td></td>
</tr>
</tfoot>
<tfoot>
<tr>
<td ></td>
<td >Impuestos(0%):</td>
<td>$ 0</td>
</tr>
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
<p style="font-size: 8px; margin: 0;">Cra. 18 No. 21 - 76	Cel.: 302 752 1119	fundescguajira@gmail.com</p>
<p style="font-size: 8px; margin: 0;">Riohacha, La Guajira</p>
</div>      




</body>
</html>
`; const ventana = window.open('', '_blank');
            ventana.document.write(contenido);
            ventana.document.close();

            // Espera un momento para que todo se cargue (incluida la imagen), antes de imprimir
            ventana.onload = () => {
                setTimeout(() => {
                    ventana.focus();
                    ventana.print();
                    ventana.close();
                }, 800); // espera 1 segundo (puedes aumentar a 1500 si aún no carga la imagen)
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

            // console.log(items)          
            this.isLoading = true;
            axios.put(`/nota/${this.idEdit}`, this.form)
                .then((res) => {
                    this.notas = res.data
                    this.getNotas(this.notas)
                    console.log(this.notas)
                    //  this.created()                
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

<style>
.container {
    max-width: 1100px;
}

.table th,
.table td {
    text-align: center;
    vertical-align: middle;
}
</style>