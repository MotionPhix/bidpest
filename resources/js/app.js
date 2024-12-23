import '../css/app.css';
import './bootstrap';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';
import { MotionPlugin } from '@vueuse/motion'
import {renderApp} from '@inertiaui/modal-vue'
import {createPinia} from 'pinia'
import Toast from 'vue-toastification'
import toast from '@/Plugins/Toaster'


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({el, App, props, plugin}) {
    const app = createApp({render: renderApp(App, props)})

    const pinia = createPinia()

    app.use(plugin)
    app.use(ZiggyVue)
    app.use(MotionPlugin)
    app.use(pinia)

    app.use(Toast, {
      // Options for toast
    })

    app.config.globalProperties.$toast = toast

    app.mount(el)

    /*return createApp({render: renderApp(App, props)})
      .use(plugin)
      .use(ZiggyVue)
      .mount(el);*/
  },
  progress: {
    color: '#4B5563',
  },
});
