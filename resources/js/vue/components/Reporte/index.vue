<template>


    <main class="main" id="main">

        <div class="container-fluid">
            <div class="row">
                <div class="card col-md-12">
                    <div class="card-body">
                        <h6 align="center" class=" p-2 border col-14 col-md-12 ">Reportes</h6>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>

                                        <th>documento</th>
                                        <th>Nombre</th>
                                        <th>Año</th>
                                        <th>Semestre</th>
                                        <th>Pendiente</th>
                                        <th>Valor Pagado</th>
                                        <th>estado semestral</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="input-group-sm ">
                                                <input type="text" class="form-control" v-model="identidad"
                                                    @input="busca()">
                                            </div>
                                        </th>
                                        <th> </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody class="table-border-bottom-0">
                                    <tr v-for="item in abono" :key="item.id">
                                        <th>{{ item.documento }}</th>
                                        <th>{{ item.name }}</th>
                                        <th>{{ item.año }}</th>
                                        <th v-if="item.semestre == 1">Introductorio</th>
                                        <th v-else-if="item.semestre == 2">Primer semestre</th>
                                        <th v-else-if="item.semestre == 3">Segundo semestre</th>
                                        <th v-else-if="item.semestre == 4">Tercer semestre</th>
                                        <th v-else-if="item.semestre == 5">Cuarto semestre</th>
                                        <th v-else>Semestre no válido</th>
                                        <th>{{ item.valor }}</th>
                                        <th>{{ item.total }}</th>
                                        <th v-if="item.estad > 0"><span class="badge bg-label-primary me-1">
                                                Abierto</span>
                                        </th>
                                        <th v-else v-bind:class="[errorClass]"><span
                                                class="badge bg-label-warning me-1">
                                                Cerrado</span></th>

                                        <th v-if="item.estado > 0"><span class="badge bg-label-primary me-1">Por
                                                pagar</span>
                                        </th>
                                        <th v-else v-bind:class="[errorClass]"><span
                                                class="badge bg-label-warning me-1">
                                                Pagado</span></th>
                                        <th><button @click="reporte(item)" style="margin-right: 5px;"
                                                class="btn btn-secondary btn-sm" title="Imprimir">
                                                <i class="fa fa-print"></i>
                                            </button>


                                            <button @click="cerrar(item)" v-if="item.estad == 1 && seccion == 'Admin'"
                                                class="btn btn-danger btn-sm" title="Cerrar matricula">
                                                <i class="fa fa-lock"></i>
                                            </button>
                                            <button @click="abrir(item)" v-else
                                                v-if="item.estad == 0 && seccion == 'Admin'"
                                                class="btn btn-danger btn-sm" title="Abrir matricula">
                                                <i class="fa fa-unlock"></i>
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
                                </nav>
                                <!--/ Basic Pagination -->
                            </div>
                        </div>
                    </div>


                </div>

                <loading v-model:active="isLoading" color="#48FF09" :can-cancel="true" :on-cancel="onCancel"
                    :is-full-page="fullPage" /><br>

                <!-- Fin ejemplo de tabla Listado -->
            </div>
        </div>
    </main>
</template>

<script>
import MoneySpinner from 'v-money-spinner'
import Html2pdf from 'html2pdf.js'
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import LaravelVuePagination from '../../../../../node_modules/laravel-vue-pagination';
let users = document.head.querySelector('meta[name="user"]');
let seccion = JSON.parse(users.content).especialidad;
export default {
    components: {

        MoneySpinner,
        Loading,
        'Pagination': LaravelVuePagination,
    },
    data() {
        return {

            // id: this.$route.params.id,
            fullPage: '',
            abono: {},
            seccion: [seccion],
            errors: {},
            cred: [],
            pdf: {},
            estudent: {},
            date: {},
            cantidad: '',
            credito: '',
            fechas: '',
            fecha: '',
            names: '',
            estudiante_id: '',
            asignatura_id: '',
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
        }, total() {
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
    },
    mounted() {
        this.created();

    },

    methods: {

        changePage: function (page) {
            this.pagination.current_page = page;
            this.created(page);
        },
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor)
        },
        async created(page) {

            this.isLoading = true;
            axios.get('/reporte?page=' + page)
                .then(res => {

                    this.abono = res.data.abono.data;
                    //console.log(this.abono)
                    this.date = res.data;
                    this.pagination = res.data.pagination
                    this.isLoading = false

                }).catch(error => {
                    this.abono = [];
                    this.isLoading = false
                })

        },
        cierre() {
            if (confirm('¿Seguro que desea cerrar la matricula?')) {
                this.isLoading = true;
                axios.get('/cerrar/' + this.id)
                    .then((res) => {
                        this.r = res.data;
                        toastr.success('Se Ha Cerredo Exitosamente');

                        this.created();
                        this.isLoading = false

                    }).catch((error) => {
                        this.abono = [];
                        this.isLoading = false
                    })
            }
        },
        abierto() {
            if (confirm('¿Seguro que desea abrir la matricula?')) {
                this.isLoading = true;
                axios.get('/abrir/' + this.id)
                    .then((res) => {
                        this.r = res.data;
                        toastr.success('Se Ha abierto Exitosamente');

                        this.created();
                        this.isLoading = false

                    }).catch((error) => {
                        this.abono = [];
                        this.isLoading = false
                    })
            }
        },
        busca() {

            this.isLoading = true;
            axios.post('/reporte/busca', {
                identidad: this.identidad,

            })
                .then((res) => {
                    this.abono = res.data.abono.data;

                    this.pagination = res.data.pagination
                    this.isLoading = false

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },

        list() {

            this.isLoading = true;
            axios.post('/abono/list', {
                id: this.id,
                año: this.año,
            })
                .then((res) => {
                    this.abonolist = res.data.abono;
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
            this.isLoading = true;
            axios.post('/abono/credito', {
                id: this.id,
            })
                .then((res) => {
                    this.cred = res.data.cred;

                    this.isLoading = false

                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })
        },
        report() {
            this.isLoading = true;
            axios.post('/abono/pdf', {
                asignatura_id: this.asignatura_id,

            })
                .then((res) => {
                    this.item = res.data.pdf;



                    this.imprimir(this.item)




                }).catch((error) => {
                    this.abono = [];
                    this.isLoading = false
                })


        },

        imprimir(item) {
            const logoUrl = `${window.location.origin}/images/aishap.png`;
            console.table(item)
            // Tomar semestre del primer abono

            // Calcular total solo de este item (grupo de abonos)
            const total = item.reduce((suma, abono) => {
                const cantidad = Number(abono.cantidad);
                return suma + (isNaN(cantidad) ? 0 : cantidad);
            }, 0);

            // Generar filas para todos los abonos de este item
            let filas = '';
            item.forEach((abono) => {
                if (abono.semestre == 1) {
                    var sem = "Introductorio";
                } else
                    if (abono.semestre == 2) {
                        var sem = "primer Semestre";
                        console.log(sem)
                    } else
                        if (abono.semestre == 3) {
                            var sem = "Segundo Semestre";
                        } else
                            if (abono.semestre == 4) {
                                var sem = "Tercer Semestre";
                            } else {
                                var sem = "Cuarto Semestre"
                            }
                filas += `
            <tr>
                
                <td>${sem}</td>
                <td>${abono.formapago}</td>
                <td>${abono.fechas}</td>
                <td>${this.formatoCOP(abono.porpagar || 0)}</td>
                <td>${this.formatoCOP(abono.cantidad)}</td>
            </tr>
        `;
            });
            const fechaLarga = () => {
                const fecha = new Date();
                const dia = fecha.getDate();
                const mes = fecha.toLocaleString('es-CO', { month: 'long' });
                const anio = fecha.getFullYear();
                return `a los ${dia} días del mes de ${mes} de ${anio}`;
            };
            const contenido = `
      <html>
        <head>
          <title>Factura de Pago</title>
          <style>
            body { font-family: Arial; padding: 20px; font-size: 14px;}
            h2 { margin-bottom: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px;font-size: 14px; }
            th, td { border: 1px solid black; padding: 4px; text-align: left; }
            #ti { text-align: center; font-size: 16px; }
            #im { text-align: right; font-size: 16px; }
            p {font-size: 12px;}
            .header { margin-bottom: 10px; }
          </style>
        </head>
        <body>
          <div class="header">     
            <img src="${logoUrl}" alt="Logo" style="max-height: 60px; width: 100%;">
            <p id="ti">Fundacion para el Desarrollo Educativo y Social de la Guajira </p>
            <p id="ti"><strong>NIT:</strong> 901691754-7</p>
            <h2 style="text-align:center;">CERTIFICADO DE PAZ Y SALVO</h2>
            <p id="im"><strong>Fecha de impresión:</strong> ${new Date().toLocaleDateString('es-CO')}</p>
           
            <p style="font-size: 15px;">Yo, Angelis Deluque Galván, actuando 
                en calidad de Representante Legal de la <strong>Fundación para el Desarrollo Educativo y Social de la Guajira (AISHA S.A.S.)</strong>, identificada 
                con NIT No. <strong>901691754-7</strong>, certifico que:</p>

            <p style="font-size: 15px; margin-top:10px">El(la) estudiante <strong>${item[0].name}</strong>, identificado(a) con documento No. <strong>${item[0].documento}</strong>, 
                se encuentra a paz y salvo, sin obligación pendiente con nuestra entidad, correspondiente al semestre académico ${item[0].año}-${item[0].periodo}  .</p>

           
          </div>

          <div class="factura-container">
            <table>
              <thead>
                <tr>                             
                    <th>Semestre</th> 
                  <th>Forma Pago</th>          
                  <th>Fecha de pago</th>
                  <th>Por Pagar</th>
                  <th>Valor cancelado</th>                  
                </tr>              
              </thead>
              <tbody>${filas}</tbody>
              <tfoot>
                <tr>
                  <td colspan="3"></td>
                  <th>Total:</th>
                  <th>${this.formatoCOP(total)}</th>
                </tr>
              </tfoot>
            </table>
          </div>
          <br>
          <p style="font-size: 15px; margin-top:10px">
    Este certificado se expide a solicitud del interesado y para los fines que estime convenientes, 
    ${fechaLarga()}.
  </p>
          <br><br>
          <div style="text-align: left;">
            <img src="${window.location.origin}/images/firma.png" alt="Firma" style="width: 100px;">
            <p style="font-size: 15px; "><strong>Anyelis Deluque Galván</strong></p>
            <p style="font-size: 15px; ">Representante Legal</p>
          </div>

          <div style="text-align: center; margin-top:40px;">
            <p><strong>RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013</strong></p>
            <p>Cra. 18 No. 21 - 76 Cel.: 302 752 1119 fundescguajira@gmail.com</p>
            <p>Riohacha, La Guajira</p>
          </div>      
        </body>
      </html>
    `;

            const ventana = window.open('', '_blank');
            ventana.document.write(contenido);
            ventana.document.close();

            ventana.onload = () => {
                setTimeout(() => {
                    ventana.focus();
                    ventana.print();
                    ventana.close();
                }, 800);
            };
            this.created();
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

            this.isLoading = true;
            axios.post('/abono', {
                credito: this.credito,
                estudiante_id: this.id,
                cantidad: this.cantidad,
                fecha: this.fecha,
                fechas: this.fechas,
                estado: this.estado,

            })
                .then(res => {
                    this.success = true;
                    this.created();
                    if (res.data != 'no') {
                        toastr.success('Se Abono Exitosamente');
                    } else {
                        toastr.warning('El valor que desea abonar sobre pasa el valor a deber');
                    }


                    this.name = '';
                    this.estado = '';
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
                    } else if (error.response.status === 500) {
                        this.errors = error.response.data.errors;
                    }
                })

            this.created();
        },
        editar(datos) {
            this.id = datos.id;
            this.cantidad = datos.cantidad;
            this.credito = datos.credito
            uno.hidden = false
            dos.hidden = true
        },
        abrirmodaledit(datos) {
            cantid.disabled = false
            fech.disabled = false
            estad.disabled = false
            this.id = datos.id;
            this.btncrear = true;
            this.btncancel = true;
            uno.hidden = false
            dos.hidden = true
            this.credit()
            $('#modalCenter').modal('show');

        },
        pagos(datos) {
            cantid.disabled = false
            fech.disabled = false
            estad.disabled = false
            this.id = datos.id;
            this.año = datos.año
            this.btncrear = true;
            this.btncancel = true;
            dos.hidden = false
            uno.hidden = true
            this.list()
            $('#modalCenter').modal('show');

        },
        reporte(datos) {

            this.id = datos.id;
            this.año = datos.año;
            this.semestre = datos.semestre;
            this.asignatura_id = datos.asignatura_id;
            this.report()

        },
        cerrar(datos) {
            this.id = datos.asignatura_id;
            this.cierre()

        },
        abrir(datos) {
            this.id = datos.asignatura_id;
            this.abierto()

        }

    },


}

</script>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 20px;
}

.cert-container {
    max-width: 700px;
    margin: auto;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

.header h1 {
    font-size: 22px;
    margin: 0;
}

.subheader {
    font-size: 16px;
    margin-top: 5px;
    color: #555;
}

.title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin: 30px 0;
    text-decoration: underline;
}

.content {
    font-size: 16px;
    line-height: 1.6;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.data-table td {
    padding: 10px;
    border: 1px solid #ccc;
}

.footer {
    margin-top: 30px;
}

.signature {
    margin-top: 40px;
}
</style>
<style>
.s1 {
    color: black;
    font-family: "Arial Narrow", sans-serif;
    font-style: normal;
    font-weight: normal;
    text-decoration: none;
    font-size: 14pt;
}

.s2 {
    color: black;
    font-family: "Arial Narrow", sans-serif;
    font-style: normal;
    font-weight: normal;
    text-decoration: none;
    font-size: 11pt;
}

p {
    color: black;
    font-family: Calibri, sans-serif;
    font-style: normal;
    font-weight: normal;
    text-decoration: none;
    font-size: 12pt;
    margin: 0pt;
}

.s3 {
    color: black;
    font-family: Calibri, sans-serif;
    font-style: normal;
    font-weight: normal;
    text-decoration: none;
    font-size: 12pt;
}

table,
tbody {
    vertical-align: top;
    overflow: visible;
}
</style>