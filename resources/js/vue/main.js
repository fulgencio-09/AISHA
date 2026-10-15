import VueApexCharts from 'vue3-apexcharts'
import * as Vue from 'vue';
import App from './components/App.vue';
import * as VueRouter from 'vue-router'
window.Vue = require('vue').default;
import Home from './components/Form/Home.vue';
import User from './components/User/index.vue';
import Edit from './components/User/Edit.vue';
import Alumno from './components/Alumnos/index.vue';
import Matricula from './components/Asignaturas/index.vue';
import Abonos from './components/Abonos/index.vue';
import Seguro from './components/Seguro/index.vue';
import Notas from './components/Notas/index.vue';
import Reporte from './components/Reporte/index.vue';
import Informe from './components/Reporte/relacion.vue';
import Password from './components/user/contrasena.vue';

// Todas las alertas nativas del sistema se muestran mediante Toastr.
// Esto permite mantener un único mecanismo visual para los mensajes.
window.__toastrErrorMessage = null;
window.alert = function (message) {
  const mensaje = window.__toastrErrorMessage || message || 'Ha ocurrido un error.';
  window.__toastrErrorMessage = null;

  if (window.toastr) {
    window.toastr.error(mensaje);
  } else {
    console.error(mensaje);
  }
};

// Captura los mensajes enviados por Laravel en errores HTTP para que
// cualquier componente que use alert() pueda mostrar el mensaje real mediante Toastr.
if (typeof axios !== 'undefined') {
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
}

const routes = [
  {
    path: '/cambio/password',
    component: Password,
  },
  {
    path: '/relacion',
    component: Informe,
  },
  {
    path: '/home',
    component: Home,
  },
  {
    path: '/user/index',
    component: User,
    name: 'usuario'
  },
  {
    path: '/user/:id',
    component: Edit,
    name: 'edit'
  },
  {
    path: '/alumno/index',
    component: Alumno,
  },
  {
    path: '/matricula/:id',
    component: Matricula,
  },
  {
    path: '/abono/index',
    component: Abonos,
  },
  {
    path: '/seguro/index',
    component: Seguro,
  },
  {
    path: '/nota/index',
    component: Notas,
  },
  {
    path: '/reporte/index',
    component: Reporte,
  },
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
