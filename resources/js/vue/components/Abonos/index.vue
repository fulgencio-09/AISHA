<template>

    <main class="main" id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="card col-md-12" id="tres">
                    <div class="card-body">
                        <h6 align="center" class=" border p-1 col-md-12">Matriculados </h6>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Nombre</th>
                                        <th>Sede</th>
                                        <th>Año</th>
                                        <th>Período</th>
                                        <th>Semestre</th>
                                        <th>Pendiente</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="input-group-sm ">
                                                <input type="search" id="searchbox" class="form-control"
                                                    v-model="identidad" @input="busca()">
                                            </div>
                                        </th>
                                        <th> </th>

                                        <th>

                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody class="table-border-bottom-0">
                                    <tr v-for="item in abonos" :key="item.id">
                                        <th>{{ item.documento }}</th>
                                        <th>{{ item.name }}</th>
                                        <th>{{ item.sede }}</th>
                                        <th>{{ item.año }}</th>
                                        <th>{{ item.periodo }}</th>
                                        <th v-if="item.semestre == 1">Introductorio</th>
                                        <th v-else-if="item.semestre == 2">Primer semestre</th>
                                        <th v-else-if="item.semestre == 3">Segundo semestre</th>
                                        <th v-else-if="item.semestre == 4">Tercer semestre</th>
                                        <th v-else-if="item.semestre == 5">Cuarto semestre</th>
                                        <th v-else>Semestre no válido</th>
                                        <th>{{ formatoCOP(item.valor) }}</th>

                                        <th v-if="item.valor > 0"><span class="badge bg-label-primary me-1">Por
                                                pagar</span>
                                        </th>
                                        <th v-else v-bind:class="[errorClass]"><span
                                                class="badge bg-label-warning me-1">
                                                Pagó</span></th>
                                        <th>
                                            <button class="btn btn-success btn-sm" title="Agregar pagos"
                                                @click="abrirmodaledit(item)" style="margin-right: 5px;">
                                                <i class="fa fa-usd"></i>
                                            </button>

                                            <button @click="pagado(item)" class="btn btn-secondary btn-sm"
                                                title="Pagos realizados">
                                                <i class="fa fa-clipboard"></i>
                                            </button>

                                        </th>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
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
                                    <div class="d-flex justify-content-end gap-2">
                                        <button @click.prevent="created()" v-if="/\d/.test(identidad)" type="button"
                                            class="btn-sm btn btn-outline-danger"> <i class='fa fa-ban'></i>
                                            Salir</button>

                                    </div>
                                </nav>

                                <!--/ Basic Pagination -->
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-12 mt-2" id="uno">
                    <div class="card ">
                        <div class="card-body">
                            <h6 align="center" class=" border p-1 col-md-12"><label for="nameWithTitle"
                                    class="form-label">formulario para la realizacion de aportes de pagos</label></h6>
                            <div class="modal-body border p-2">
                                <div class="row">

                                    <div class="col-12 col-sm-3 input-group-sm">
                                        <label class="form-label">Año</label>
                                        <input type="text" class="form-control" :value="año" readonly>
                                    </div>

                                    <div class="col-12 col-sm-3 input-group-sm">
                                        <label class="form-label">Período</label>
                                        <input type="text" class="form-control" :value="periodo" readonly>
                                    </div>

                                    <div class="col-12 col-sm-3 input-group-sm">
                                        <label class="form-label">Semestre</label>
                                        <input type="text" class="form-control" :value="nombreSemestre" readonly>
                                    </div>

                                    <div class="col-12 col-sm-3 input-group-sm">
                                        <label class="form-label">Crédito</label>
                                        <input type="text" class="form-control" :value="creditoFormateado" readonly>
                                    </div>




                                    <div class="col-14 col-sm-3 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">Fecha</label>
                                        <input type="date" :min="hoy" id="cantid" class="form-control" v-model="fechas">
                                    </div>
                                    <input type="hidden" id="cantid" class="form-control" v-model="name">
                                    <input type="hidden" id="cantid" class="form-control" v-model="documento">
                                    <input type="hidden" id="cantid" class="form-control" v-model="user_id">
                                    <input type="hidden" id="cantid" class="form-control" v-model="deuda">
                                    <input type="hidden" id="cantid" class="form-control" v-model="semestre">

                                    <div class="col-14 col-sm-3 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">Forma Pago</label>
                                        <select class="form-select" id="fech" aria-label="Multiple select example"
                                            v-model="formapago">
                                            <option value="">seleccione</option>
                                            <option value="Bancolombia">Bancolombia</option>
                                            <option value="Nequi">Nequi</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Davivienda">Davivienda</option>
                                            <option value="Daviplata">Daviplata</option>
                                            <option value="Escuela">Escuela</option>

                                        </select>
                                    </div>
                                    <div class="col-14 col-sm-3 input-group-sm ">
                                        <label for="nameWithTitle" class="form-label">cantidad</label>
                                        <input type='text' placeholder="$" class="form-control" name="canti" id="canti"
                                            :value="precioFormateado" @input="actualizarPrecio" />

                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="d-flex justify-content-end gap-2" id="cuatro">
                                <button @click.prevent="agregar(e)" v-if="btncrear" type="submit"
                                    class="btn-sm btn btn-outline-success"><i class="fa fa-floppy-o"
                                        aria-hidden="true"></i> Guardar</button>
                                <button @click.prevent="update()" v-if="btnedit" type="button"
                                    class="btn btn-outline-info">
                                    <i class="fa-sm fa-refresh" aria-hidden="true"></i> Actualizar
                                </button>
                                <button @click.prevent="created()" v-if="btncrear" type="button"
                                    class="btn-sm btn btn-outline-danger"> <i class='fa fa-ban'></i>
                                    Cancelar</button>

                            </div>
                        </div>

                    </div>
                    <div class="card mt-5" id="pagado">
                        <div class="card-body">
                            <div class="table-responsive text-nowrap">
                                <h6 align="center" class=" border p-1 col-md-12"><label for="nameWithTitle"
                                        class="form-label">Listado de pagos realizado por el estudiante</label></h6>
                                <table id="ejemplos" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            <th>Documento</th>
                                            <th>semestre</th>
                                            <th>fecha</th>
                                            <th>Deuda</th>
                                            <th>Cantidad</th>
                                            <th>accion</th>

                                        </tr>

                                    </thead>

                                    <tbody class="table-border-bottom-0">
                                        <tr v-for="item in pagos" :key="item.id">
                                            <th>{{ item.name }}</th>
                                            <th>{{ item.documento }}</th>
                                            <th v-if="item.semestre == 1">Introductorio</th>
                                            <th v-else-if="item.semestre == 2">Primer semestre</th>
                                            <th v-else-if="item.semestre == 3">Segundo semestre</th>
                                            <th v-else-if="item.semestre == 4">Tercer semestre</th>
                                            <th v-else-if="item.semestre == 5">Cuarto semestre</th>
                                            <th v-else>Semestre no válido</th>
                                            <th>{{ item.fechas }}</th>
                                            <th>{{ formatoCOP(item.deuda) }}</th>
                                            <th>{{ formatoCOP(item.cantidad) }}</th>
                                            <th><button @click="imprimir(item)" style="margin-right: 5px;"
                                                    class="btn btn-secondary btn-sm" title="Imprimir">
                                                    <i class="fa fa-print"></i>
                                                </button>

                                                <button v-if="item.valor > '0' && seccion == 'Admin'"
                                                    @click="confirmDelete(item, user_id)" class="btn btn-danger btn-sm"
                                                    title="Anular">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- <div class="card mt-2" id="dos">
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <h6 align="center" class=" border p-1 col-md-12"><label for="nameWithTitle"
                                    class="form-label">Listado de pagos realizado por el estudiante</label></h6>
                            <table id="ejemplos" class="table table-bordered">
                                <thead>
                                    <tr>

                                        <th>fecha</th>
                                        <th>Cantidad</th>
                                        <th>cajero</th>
                                        <th>accion</th>

                                    </tr>

                                </thead>

                                <tbody class="table-border-bottom-0">
                                    <tr v-for="(item, index) in abonolist" :key="index">
                                        <td>{{ item.fechas }}</td>
                                        <td>{{ formatoCOP(item.cantidad) }}</td>
                                        <td>{{ item.nombre }}</td>

                                        <td><button @click="imprimir(item)" style="margin-right: 5px;"
                                                class="btn btn-secondary btn-sm" title="Imprimir">
                                                <i class="fa fa-print"></i>
                                            </button>
                                            <!--    <button @click="enviar(item)" style="margin-right: 5px;"
                                                class="btn btn-success btn-sm" title="Enviar">
                                                <i class="fa fa-whatsapp"></i>
                                            </button>-->

                                         <!--    <button
                                                v-if="item.valor > '0' && seccion == 'Admin' && index === abonolist.length - 1"
                                                @click="confirmDelete(item, user_id)" class="btn btn-danger btn-sm"
                                                title="Anular">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </td>



                                    </tr>

                                </tbody>

                                <tr>
                                    <th>Total</th>

                                    <th>{{ formatoCOP(total) }}</th>
                                    <th></th>

                                    <td></td>
                                </tr>
                            </table>
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <button @click="created(item)" class="btn btn-warning btn-sm" title="Ocultar">
                                    <i class="fa fa-reply-all"></i> Salir
                                </button>
                            </div>


                        </div>
                    </div>

                </div>  -->

                <div class="card mt-2" id="dos">
    <div class="card-body">
        <div class="table-responsive text-nowrap">

            <h6 align="center" class="border p-1 col-md-12">
                <label for="nameWithTitle" class="form-label">
                    Listado de pagos realizado por el estudiante
                </label>
            </h6>

            <table id="ejemplos" class="table table-bordered">

                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cantidad</th>
                        <th>Cajero</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody class="table-border-bottom-0">

                    <tr
                        v-for="(item, index) in abonolist"
                        :key="index"
                    >

                        <th>
                            {{ item.fechas }}
                        </th>

                        <th>
                            {{ formatoCOP(item.cantidad) }}
                        </th>

                        <th>
                            {{ item.nombre }}
                        </th>

                        <th>

                            <button
                                @click="imprimir(item)"
                                style="margin-right: 5px;"
                                class="btn btn-secondary btn-sm"
                                title="Imprimir"
                            >
                                <i class="fa fa-print"></i>
                            </button>

                            <!--
                            <button
                                @click="enviar(item)"
                                style="margin-right: 5px;"
                                class="btn btn-success btn-sm"
                                title="Enviar"
                            >
                                <i class="fa fa-whatsapp"></i>
                            </button>
                            -->

                            <button
                                v-if="
                                    item.valor > '0' &&
                                    seccion == 'Admin' &&
                                    index === abonolist.length - 1
                                "
                                @click="confirmDelete(item, user_id)"
                                class="btn btn-danger btn-sm"
                                title="Anular"
                            >
                                <i class="fa fa-trash-o"></i>
                            </button>

                        </th>

                    </tr>

                </tbody>

                <!-- TOTAL -->
                <tfoot>
                    <tr>
                        <th>Total</th>

                        <th>
                            {{ formatoCOP(total) }}
                        </th>

                        <th></th>

                        <th></th>
                    </tr>
                </tfoot>

            </table>

            <div class="d-flex justify-content-end gap-2 mt-2">

                <button
                    @click="created(item)"
                    class="btn btn-warning btn-sm"
                    title="Ocultar"
                >
                    <i class="fa fa-reply-all"></i>
                    Salir
                </button>

            </div>

        </div>
    </div>
</div>

                <p id="ejemplo"></p>
                <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                    :is-full-page="fullPage" /><br>

                <!-- Fin ejemplo de tabla Listado -->
            </div>
        </div>
    </main>

</template>

<script>
import Swal from "sweetalert2";
import MoneySpinner from 'v-money-spinner'
import Html2pdf from 'html2pdf.js'
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
let users = document.head.querySelector('meta[name="user"]');
let seccion = JSON.parse(users.content).especialidad;
let user_id = JSON.parse(users.content).id;
export default {

    components: {
        MoneySpinner,
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {

            id: this.$route.params.id,
            name: '',
            documento: '',
            fullPage: '',
            abonos: [],
            errors: {},
            seccion: [seccion],
            user_id: user_id,
            cred: {},
            movimiento: '',
            dos: true,
            pagos: {},
            uno: true,
            use: true,
            pdf: {},
            estudent: {},
            date: {},
            credito: '',
            creditoSeleccionado: '',
            deuda: '',
            formapago: '',
            asignatura_id: '',
            cantidad: '',
            valor: '',
            fechas: '',
            semestre: '',
            ano: '',
            identidad: '',
            documento: '',
            mes: '',
            dia: '',
            hoy: new Date().toISOString().split('T')[0],
            año: '',
            periodo: '',
            names: '',
            abonolist: [],
            estudiante_id: '',
            estado: '',
            isLoading: false,
            activeClass: 'text-success',
            errorClass: 'text-danger',
            btnedit: false,
            btncrear: false,
            onCancel: this.onCancel,
            id: '',
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
        total2() {
            if (!Array.isArray(this.abonos)) return 0;

            return this.abonos.reduce((suma, item) => {
                const valor = Number(item.valor);
                return suma + (isNaN(valor) ? 0 : valor);
            }, 0);
        },
        total() {
            if (!Array.isArray(this.abonolist)) return 0;

            return this.abonolist.reduce((suma, item) => {
                const cantidad = Number(item.cantidad);
                return suma + (isNaN(cantidad) ? 0 : cantidad);
            }, 0);
        },
        precioFormateado() {
            return this.cantidad > 0
                ? '$ ' + new Intl.NumberFormat('es-CO').format(this.cantidad)
                : ''
        },
        creditoFormateado() {
            return this.creditoSeleccionado !== '' ? this.formatoCOP(this.creditoSeleccionado) : '';
        },
        nombreSemestre() {
            const semestres = {
                1: 'Introductorio',
                2: 'Primer semestre',
                3: 'Segundo semestre',
                4: 'Tercer semestre',
                5: 'Cuarto semestre'
            };
            return semestres[this.semestre] || 'Semestre no válido';
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
        this.created();

    },

    methods: {

        imprimir(item) {
           // console.table(item.semestre)
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            if (item.semestre == 1) {
                var sem = "Introductorio";
            } else
                if (item.semestre == 2) {
                    var sem = "primer Semestre";
                    console.log(sem)
                } else
                    if (item.semestre == 3) {
                        var sem = "Segundo Semestre";
                    } else
                        if (item.semestre == 4) {
                            var sem = "Tercer Semestre";
                        } else {
                            var sem = "Cuarto Semestre"
                        }
            const total = this.abonolist.reduce((suma, item) => {
                const cantidad = Number(item.cantidad);
                return suma + (isNaN(cantidad) ? 0 : cantidad);
            }, 0);
            const contenido = `
      <html>
        <head>
          <title align="center">Factura de Pago</title>
          <style>
            body { font-family: Arial; padding: 20px; font-size: 14px;}
            h2 { margin-bottom: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 5px;font-size: 12px; }
            th { border-bottom: 1px solid black;  font-size: 12px;}
            th { border-top: 1px solid black;  } 
            #ti {  text-align: center; font-size: 12px; }
            #im {   text-align: right; font-size: 12px; }
            p {font-size: 12px;}
            .linea { margin: 10px 0; border: none; border-top: 1px solid #ccc; }
            .header { margin-bottom: 1px; }
             .item { margin-bottom: 1px; }
          </style>
        </head>
        <body>
          <div class="header">     
                               
            <img src="${logoUrl}" alt="Logo" style="max-height: 60px; margin-right:30px;width: 100%; ">
            <p id="ti">Fundacion para el Desarrollo Educativo y Social de la Guajira </p>
            <p id="ti"><strong>NIT:</strong> 901691754-7</p>
            <h3>Factura de Pago</h3>
            <p id="im"><strong>Fecha de impresión:</strong> ${new Date().toLocaleDateString('es-CO')}</p>
           
           
            <div class="info-cliente">
            <p><strong>Estudiante:</strong> ${item.name}</p>
            <p></p>
           
          </div>
        </div>
         <div class="factura-container">


          <table>
            
            <thead>
                
                <tr>                              
                <th>Descripción</th>  
                <th>Forma Pago</th>          
                <th>Fecha de pago </th>
                <th>Por Pagar </th>
                <th>Valor cancelado</th>                  
              </tr>              
            </thead>
            
            <tbody>
             
                <tr>
                 
                  <th>Pago de matricula perteneciente al ${sem}</th>
                  <th>${item.formapago}</th>
                  <th>${item.fechas}</th>
                  <th>${this.formatoCOP(item.deuda)}</th></th>
                  <th>${this.formatoCOP(item.cantidad)}</th>
                </tr>
              
            </tbody>
            
           
            <tfoot>
              <tr>
                <td ></td>
                <td ></td>
                <td></td>
                <th >Total:</th>
                <th>${this.formatoCOP(item.cantidad)}</th>
              </tr>
            </tfoot>
            <tfoot>
              <tr>
                <td ></td>
                <td ></td>
                <td ></td>
                <td >Sub Total:</td>
                <td>${this.formatoCOP(item.cantidad)}</td>
              </tr>
            </tfoot>
            <tfoot>
              <tr>
                <td ></td>
                <td ></td>
                <td ></td>
                <td >  Impuestos(0%):</td>
                <td>$ 0</td>
              </tr>
            </tfoot>
            
          </table>

        </div>
        <br>
        <div style="text-align: left;">
  <img src="${window.location.origin}/images/firma.png" alt="Firma" style="width: 100px; height: auto;">
  <p style="font-size: 12px; margin: 0;"><strong>Anyelis Deluque Galván</strong></p>
  <p style="font-size: 12px; margin: 0;">Representante Legal</p>
</div>  
<div style="text-align: center; margin-top:5px;">
 
  <p style="font-size: 9px; margin: 0;"><strong>RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013</strong></p>
  <p style="font-size: 9px; margin: 0;">Cra. 18 No. 21 - 76	Cel.: 302 752 1119	fundescguajira@gmail.com</p>
  <p style="font-size: 9px; margin: 0;">Riohacha, La Guajira</p>
</div>      


 

        </body>
      </html>
    `;

            const ventana = window.open('', '_blank');
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
        async enviar(item) {

            try {
                const response = await axios.post(`/facturas`, {
                    to: "57" + item.telefono,
                    name: item.name,
                    documento: item.documento,
                    semestre: item.semestre,
                    formapago: item.formapago,
                    fecha: item.fechas,
                    deuda: item.deuda,
                    cantidad: item.cantidad
                });
                console.log(response.data);
                alert("Mensaje enviado con éxito a WhatsApp.");
            } catch (error) {
                console.error(error);
                alert("Error al enviar mensaje por WhatsApp.");
            }

        },

        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor)
        },
        actualizarPrecio(event) {
            const valorLimpio = event.target.value.replace(/[^\d]/g, '')
            this.cantidad = parseInt(valorLimpio) || 0

        },

        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },

        async created(page) {

            this.isLoading = true;
            axios.get('/abono?page=' + page)
                .then(res => {
                    tres.hidden = false
                    uno.hidden = true
                    dos.hidden = true

                    cuatro.hidden = true
                    cantid.disabled = true
                    fech.disabled = true
                    this.btncrear = false;
                    this.btnedit = false;
                    this.abonos = res.data.abono.data;
                    this.date = res.data;
                    this.pagination = res.data.pagination
                    this.identidad = '';
                    this.isLoading = false


                }).catch(error => {
                    this.abono = [];
                    this.isLoading = false
                })

        },

        limpiar() {
            this.cantidad = '';
            this.formapago = '';
            //  this.notas();

        },


        busca() {
            this.btncrear = true;
            this.isLoading = true;
            axios.post('/abono/busca', {
                identidad: this.identidad,
            })
                .then((res) => {
                    this.abonos = res.data.abono.data;
                    this.pagination = res.data.pagination
                    tres.hidden = false
                    uno.hidden = true
                    dos.hidden = true
                    this.fechas = '';
                    this.cantidad = '';
                    this.año = '';
                    this.periodo = '';
                    this.movimiento = '';
                    this.formapago = '';
                    this.asignatura_id = '';
                    this.creditoSeleccionado = '';

                    this.isLoading = false

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        buscar() {

            this.isLoading = true;
            axios.post('/abono/buscar', {
                fecha: this.names,
                estudiante_id: this.id,
            })
                .then((res) => {
                    this.abonolist = res.data.abono;
                    // console.log(this.abonolist)
                    this.isLoading = false
                    $(document).ready(function () {
                        var total_col2 = 0;
                        $('#ejemplos tbody').find('tr').each(function (i, el) {
                            total_col2 += parseFloat($(this).find('td').eq(2).text());
                        });
                        $('#ejemplos tfoot tr th').eq(2).text(total_col2);

                    });
                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        list() {
            // use.hidden = true
            this.isLoading = true;
            axios.post('/abono/list', {
                id: this.id,
                asignatura_id: this.asignatura_id,
            })
                .then((res) => {
                    this.abonolist = res.data.abono;
                    //  console.log(this.abonolist)
                    $(document).ready(function () {
                        var total_col2 = 0;
                        $('#ejemplos tbody').find('tr').each(function (i, el) {
                            total_col2 += parseFloat($(this).find('td').eq(2).text());
                        });
                        $('#ejemplos tfoot tr th').eq(2).text(total_col2);
                    });
                    this.isLoading = false
                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        credit() {
            pagado.hidden = true
            this.isLoading = true;
            axios.get('/abono/credito/' + this.asignatura_id)
                .then((response) => {
                    this.cred = response.data.cred;

                    if (this.cred.length > 0) {
                        this.name = this.cred[0].name; // lo guardas en el input
                        this.documento = this.cred[0].documento;
                        this.deuda = this.cred[0].valor;
                        this.creditoSeleccionado = this.cred[0].valor;
                        this.año = this.cred[0].año;
                        this.ano = this.cred[0].año;
                        this.periodo = this.cred[0].periodo;
                        this.semestre = this.cred[0].semestre;
                        this.telefono = this.cred[0].telefono;
                    } else {
                        this.names = ''; // vacío si no hay datos
                    }

                    this.isLoading = false

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        //eliminar abonos
        confirmDelete(item) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.eliminar(item, user_id);
                }
            });
        },

        eliminar(item, user_id) {
            //console.log( user_id);         
            this.isLoading = true;
            axios.get(`/abono/eliminar/${item.id}/${user_id}`)
                .then((response) => {
                    this.debitado = response.data.debitado;
                    Swal.fire("Eliminado", "El registro fue eliminado", "success");
                    this.$emit("refresh");
                    this.list()

                    //console.log(this.credi)
                    this.isLoading = false

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        report() {
            // console.log(this.asignatura_id)
            this.isLoading = true;
            axios.post('/abono/pdf', {
                id: this.id,
                año: this.año,
                periodo: this.periodo,
                asignatura_id: this.asignatura_id,
            })
                .then((res) => {
                    this.pdf = res.data.pdf;
                    this.estudent = res.data.estudent;
                    this.ano = res.data.ano;
                    this.mes = res.data.mes;
                    this.dia = res.data.dia;
                    $(document).ready(function () {
                        var total_col2 = 0;
                        $('#ejemplo tbody').find('tr').each(function (i, el) {
                            total_col2 += parseFloat($(this).find('td').eq(2).text());
                        });
                        $('#ejemplo tfoot tr th').eq(2).text(total_col2);

                    });
                    setTimeout(function () {
                        var element = document.getElementById('primero');
                        var opt = {
                            margin: 1,
                            filename: 'CERTIFICADO DE PAZ Y SALVO',

                            html2canvas: { scale: 2 },
                            jsPDF: {
                                unit: 'in', format: 'letter', orientation: 'portrait'
                            }
                        }
                        Html2pdf().from(element).set(opt).save();
                    }, 5000);
                    this.created()

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })


        },

        update() {
            this.validateForm();
            this.isLoading = true;
            axios.put('/abono/' + this.id, {
                name: this.name,
                documento: this.documento,
                direccion: this.direccion,
                telefono: this.telefono,
                estado: this.estado,
            })
                .then(res => {
                    this.success = true;
                    if (res.data != 'no') {

                        toastr.success('Se Ha Actualizado Exitosamente');

                    } else {

                        toastr.warning('Ya esta abono existe, Se actualizaron los demas campos');

                    }

                    this.created();
                    this.isLoading = false

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

        },
        agregar() {
            if (!this.asignatura_id) {
                toastr.warning('El crédito es obligatorio');
                return;
            }
            if (!this.fechas) {
                toastr.warning('El campo fechas es obligatorio');
                return;
            }
            if (!this.cantidad) {
                toastr.warning('El campo cantidad es obligatorio');
                return;
            }
            if (!this.formapago) {
                toastr.warning('El campo forma de pago es obligatorio');
                return;
            }
            this.deuda = this.deuda - this.cantidad
            this.isLoading = true;
            axios.post('/abono', {
                asignatura_id: this.asignatura_id,
                cantidad: this.cantidad,
                semestre: this.semestre,
                fechas: this.fechas,
                formapago: this.formapago,
                documento: this.documento,
                deuda: this.deuda,
                user_id: this.user_id,
                name: this.name,
                estado: 1
            })
                .then(res => {
                    this.success = true;

                    if (res.data != 'no') {
                        toastr.success('Se abonó exitosamente');
                        this.pagos = res.data;
                        // console.table(this.pagos)
                        this.asignatura_id = '';
                        this.creditoSeleccionado = '';
                        this.fechas = '';
                        this.cantidad = '';
                        this.movimiento = '';
                        this.deuda = '';
                        this.formapago = '';
                        this.estado = '';
                        pagado.hidden = false
                    } else {
                        toastr.warning('El valor que desea abonar sobrepasa el valor a deber');
                    }

                    // this.resetForm();
                    this.isLoading = false;
                })
                .catch(() => {
                    alert('Error al guardar los datos');
                    this.isLoading = false;
                });
        },


        editar(datos) {
            this.id = datos.id;
            this.cantidad = datos.cantidad;
            this.credito = datos.credito
            uno.hidden = false
            dos.hidden = true
        },
        abrirmodaledit(datos) {
            //console.table( datos)
            this.fechas = '';
            this.cantidad = '';
            this.año = '';
            this.formapago = '';
            this.periodo = '';
            this.asignatura_id = '';
            cantid.disabled = false
            fech.disabled = false
            this.asignatura_id = datos.asignatura_id;
            this.btncrear = true;
            this.btncancel = true;
            tres.hidden = true
            uno.hidden = false
            dos.hidden = true
            this.credit()
            //$('#modalCenter').modal('show');

        },
        pagado(datos) {
            //console.table(datos) 
            cantid.disabled = false
            fech.disabled = false
            this.id = datos.id;
            this.año = datos.año
            this.asignatura_id = datos.asignatura_id
            this.btncrear = true;
            this.btncancel = true;
            dos.hidden = false
            uno.hidden = true
            tres.hidden = true

            this.list()
            $('#modalCenter').modal('show');

        },
        reporte(datos) {

            this.id = datos.id;
            this.año = datos.año;
            this.periodo = datos.periodo;
            this.report()

        }

    },


}

</script>
<style scoped>
.factura-container {
    background: #fff;
    color: #000;
    padding: 20px;
    max-width: 700px;
    margin: 0 auto;
    border: 1px solid #ccc;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    font-size: xx-small;
}

label {
    text-align: center;
}

.acciones {

    margin-top: 20px;

    text-align: right;
}
</style>