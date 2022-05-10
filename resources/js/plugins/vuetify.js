import '@mdi/font/css/materialdesignicons.css'
import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import theme from './theme'

Vue.use(Vuetify, {
  iconfont: 'mdi'
})

const options = {
  theme: theme
}

export default new Vuetify(options)
