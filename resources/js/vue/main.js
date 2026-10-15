import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts'
import * as Vue from 'vue';
import App from './components/App.vue';
import * as VueRouter from 'vue-router'
window.Vue = require('vue').default;
import Home from './components/Form/Home.vue';
import User from './components/User/index.vue';
import Edit from './components/User/Edit.vue';
import Alumno from './components/Alumnos/index.vue';
import Matricula from './components/Asignaturas/MatriculaForm.vue';
import Abonos from './components/Abonos/index.vue';
import Seguro from './components/Seguro/index.vue';
import Notas from './components/Notas/index.vue';
import Reporte from './components/Reporte/index.vue';
import Informe from './components/Reporte/relacion.vue';
import Password from './components/user/contrasena.vue';

// Unificar las alertas nativas del sistema mediante Toastr.
window.__toastrErrorMessage = null;
window.alert = function (message) {
  const esErrorBackend = !!window.__toastrErrorMessage;
  const mensaje = window.__toastrErrorMessage || message || 'Ha ocurrido un error.';
  window.__toastrErrorMessage = null;

  if (!window.toastr) {
    console.error(mensaje);
    return;
  }

  if (esErrorBackend || /error|no se puede|no fue posible|falló|fallo/i.test(mensaje)) {
    window.toastr.error(mensaje);
  } else if (/éxito|exitosamente|guardado|actualizado|enviado|correctamente/i.test(mensaje)) {
    window.toastr.success(mensaje);
  } else if (/advertencia|pendiente|debe|obligatorio/i.test(mensaje)) {
    window.toastr.warning(mensaje);
  } else {
    window.toastr.info(mensaje);
  }
};

axios.interceptors.response.use(
  response => response,
  error => {
    const data = error?.response?.data;
    window.__toastrErrorMessage =
      data?.message ||
      data?.error ||
      (typeof data === 'string' ? data : null) ||
      'No fue posible completar la operación.';

    return Promise.reject(error);
  }
);

const routes = [
  { path: '/cambio/password', component: Password },
  { path: '/relacion', component: Informe },
  { path: '/home', component: Home },
  { path: '/user/index', component: User, name: 'usuario' },
  { path: '/user/:id', component: Edit, name: 'edit' },
  { path: '/alumno/index', component: Alumno },
  { path: '/matricula/:id', component: Matricula },
  { path: '/abono/index', component: Abonos },
  { path: '/seguro/index', component: Seguro },
  { path: '/nota/index', component: Notas },
  { path: '/reporte/index', component: Reporte },
];

const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes,
});

const app = Vue.createApp(App).use(router)
app.config.globalProperties.$http = {
  formatIsoDateTime(isoString) {
    return utils_formatIsoDateTime(isoString)
  }
}

app.mount('#app');
app.use(VueApexCharts)
app.config.globalProperties.$http = axios
