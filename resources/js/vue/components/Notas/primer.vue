<template>
    <div class="card col-md-8 mx-auto">
        <div class="card-body">
            <label align="center" style="background-color: tomato; font-size: 14px; color: aliceblue;"
                class="p-1 border col-14 col-md-12">
                CALIFICACIONES
            </label>

            <!-- Datos del estudiante -->
            <table class="table table-bordered" v-if="estudiantes.length">
                <thead>
                    <tr>
                        <th>Nombre:</th>
                        <th style="font-size: 14px;">{{ estudiantes[0].name }}</th>
                    </tr>
                    <tr>
                        <th>Semestre:</th>
                        <th style="font-size: 14px;">{{ estudiantes[0].semestre }}</th>
                    </tr>
                </thead>

            </table>

            <hr>
            <form @submit.prevent="agregar">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Materias</th>
                            <th>Primer Corte</th>
                            <th>Segundo Corte</th>
                            <th>Tercer Corte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="campo in campos" :key="campo.nombre">
                            <td :for="campo.nombre">{{ campo.titulo.toUpperCase() }}</td>
                            <td v-for="p in periodos" :key="p.id">
                                <input :id="campo.nombre" v-model="formulario[campo.nombre][p.nombre]"
                                    class="form-control form-control-sm" @input="formatearNota($event, campo.nombre)"
                                    :disabled="!puedeEditar(p.fecha_inicio, p.fecha_fin)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" @click="$emit('volver')">Volver</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
            <!-- Formulario -->
            <!-- 
                <div class="row bordered">
                    <div class="col-md-6 mb-3" v-for="campo in campos" :key="campo.nombre">
                        <label :for="campo.nombre">{{ campo.titulo.toUpperCase() }}</label>
                        <input :id="campo.nombre" v-model="formulario[campo.nombre]"
                            @input="formatearNota($event, campo.nombre)" class="form-control form-control-sm" />
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" @click="$emit('volver')">Volver</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
             -->
        </div>
    </div>


</template>

<script>
export default {
    props: {
        estudiante_id: Number,
        semestre_id: Number,
        asignatura: Number,
        campos: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            formulario: {},   // { materia: { periodo_id: nota } }
            estudiantes: [],
            periodos: []
        };
    },
    watch: {
        campos: {
            immediate: true,
            handler(nuevosCampos) {
                // Solo inicializa cuando ya tengamos periodos
                if (this.periodos.length > 0) {
                    this.inicializarFormulario(nuevosCampos);
                }
            }
        }
    },
    mounted() {
        this.cargarEstudiante();
        this.cargarPeriodos();
    },
    methods: {
        async cargarPeriodos() {
            const res = await axios.get("/periodos");
            this.periodos = res.data;

            // Inicializa formulario con periodos cargados
            this.inicializarFormulario(this.campos);
        },
        puedeEditar(inicio, fin) {
            const hoy = new Date();
            const inicioDate = new Date(inicio + "T00:00:00");
            const finDate = new Date(fin + "T23:59:59");

            return hoy >= inicioDate && hoy <= finDate;
        },
        cargarEstudiante() {
            axios.get('/nota/buscar/' + this.estudiante_id)
                .then(res => {
                    this.estudiantes = res.data.nota;
                })
                .catch(() => console.warn("Error cargando estudiante"));
        },
        inicializarFormulario(campos) {
            this.formulario = {};
            campos.forEach(c => {
                this.formulario[c.nombre] = {};
                this.periodos.forEach(p => {
                    this.formulario[c.nombre][p.id] = ""; // inicializa vacío
                });
            });
        },
        formatearNota(event, campo, periodoId) {
            let entrada = event.target.value;
            if (entrada === '') {
                this.formulario[campo][periodoId] = '';
                return;
            }
            let valor = entrada.replace(',', '.').trim();
            if (/^\d{2}$/.test(valor)) {
                valor = valor[0] + '.' + valor[1];
            } /*else if (/^\d{2}$/.test(valor)) {
                valor = valor[0] + '.' + valor.slice(1, 2);
            }*/
            const numero = parseFloat(valor);
            if (!isNaN(numero) && numero >= 0 && numero <= 5) {
                const valorFormateado = numero.toFixed(1);
                this.formulario[campo][periodoId] = valorFormateado;
                event.target.value = valorFormateado;
            } else {
                event.target.value = this.formulario[campo][periodoId] ?? '';
            }
        },

        guardar() {
            const periodoActivo = this.periodos.find(p => this.puedeEditar(p.fecha_inicio, p.fecha_fin));
            const payload = {
                estudiante_id: this.estudiante_id,
                semestre: this.semestre_id,
                asignatura_id: this.asignatura,
                periodo_id: periodoActivo ? periodoActivo.id : null,
                notas: this.formulario
            };
            console.log("Enviando datos al backend:", payload);
            // axios.post('/ruta-api/guardar', payload)...
        }
        , agregar() {
            // Limpiar y tomar solo materias con nota
            const notasLimpias = {};
            for (const materia in this.formulario) {
                const valor = this.formulario[materia][this.periodo_id];
                if (valor !== '' && valor !== undefined && valor !== null) {
                    notasLimpias[materia] = valor;
                }
            }
            console.log("Enviando datos al backend:", payload);
            const payload = {
                estudiante_id: this.estudiante_id,
                semestre: this.semestre_id,
                asignatura_id: this.asignatura,
                periodo_id: this.periodo_id,
                notas: notasLimpias
            };
        }

    }
};
</script>
