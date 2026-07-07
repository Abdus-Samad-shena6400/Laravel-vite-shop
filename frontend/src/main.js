import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import store from './store'
import router from './router'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const toastOptions = {
  timeout: 3000,
  position: 'top-right',
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false
}

createApp(App)
    .use(store)
    .use(router)
    .use(Toast, toastOptions)
    .mount('#app')
