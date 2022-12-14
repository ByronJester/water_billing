import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import { InertiaProgress } from '@inertiajs/progress'; 
import VueGraph from 'vue-graph';
import GraphLine3D from 'vue-graph/src/components/line3d.js'

InertiaProgress.init({
  // The delay after which the progress bar will
  // appear during navigation, in milliseconds.
  delay: 0,

  // The color of the progress bar.
  color: '#29d',

  // Whether to include the default NProgress styles.
  includeCSS: true,

  // Whether the NProgress spinner will be shown.
  showSpinner: true,
});

InertiaProgress.init();

const app = document.getElementById('app')

Vue.use(VueGraph);
Vue.component(GraphLine3D.name, GraphLine3D);

Vue.mixin({
  methods: {
    validationError(field, errors) {
      if(errors) {
        if(errors.hasOwnProperty(field)) {
          return Array.isArray(errors[field]) ? errors[field][0] : errors[field];
        }
      }

      return null;
    }
  }
})

const pages = {
  'Login': require('./Pages/Login.vue').default,
  'Users': require('./Pages/Users.vue').default,
  'Clients': require('./Pages/Clients.vue').default,
  'Client': require('./Pages/Client.vue').default,
  'Bill': require('./Pages/Bill.vue').default,
  'Settings': require('./Pages/Settings.vue').default,
  'Profile': require('./Pages/Profile.vue').default,
  'Utilities': require('./Pages/Utilities.vue').default,
  'Reports': require('./Pages/Reports.vue').default,
  'Utility': require('./Pages/Utility.vue').default,
}

new Vue({
  data: {
      route: window.location.protocol + '//' + window.location.host
  },
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => pages[name],
    },
  }),
}).$mount(app)