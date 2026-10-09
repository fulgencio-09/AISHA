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
import Notas from './components/Notas/index.vue';
import Reporte from './components/Reporte/index.vue';
import Informe from './components/Reporte/relacion.vue';
import Password from './components/user/contrasena.vue';
const routes = [
  
 /* {
    
      path: '/',
      component: Home,
      name: 'home'
  },*/
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
      
      //usuarios
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
//Vue.prototype.$http = axios
const app = Vue.createApp(App).use(router  )
app.config.globalProperties.$http = {
  formatIsoDateTime(isoString) {
      return utils_formatIsoDateTime(isoString)
  }
}

//window.Vue.use(window.VueResource);
  app.mount('#app');
  app.use(VueApexCharts)
  app.config.globalProperties.$http = axios

  
