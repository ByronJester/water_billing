import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import { InertiaProgress } from '@inertiajs/progress'; 
import VueGraph from 'vue-graph';

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