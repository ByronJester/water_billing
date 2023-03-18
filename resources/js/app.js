import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import { InertiaProgress } from '@inertiajs/progress';

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

const pages = {
  'Home': require('./Pages/Home.vue').default,
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
