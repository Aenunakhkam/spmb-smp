import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const nuTheme = {
  dark: false,
  colors: {
    background: '#FFFFFF',
    surface: '#FFFFFF',
    primary: '#1B5E20', // Hijau NU
    'primary-darken-1': '#1B5E20',
    secondary: '#FFD600', // Kuning NU
    'secondary-darken-1': '#FBC02D',
    error: '#B00020',
    info: '#2196F3',
    success: '#4CAF50',
    warning: '#FB8C00',
  },
}

export default createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'nuTheme',
    themes: {
      nuTheme,
    },
  },
  defaults: {
    VCard: {
      elevation: 0,
    },
    VBtn: {
      elevation: 0,
      style: 'text-transform: none; letter-spacing: normal;',
    },
    VTextField: {
      variant: 'solo-filled',
      flat: true,
      color: 'primary',
      bgColor: 'grey-lighten-4',
    },
    VSelect: {
      variant: 'solo-filled',
      flat: true,
      color: 'primary',
      bgColor: 'grey-lighten-4',
    },
    VAutocomplete: {
      variant: 'solo-filled',
      flat: true,
      color: 'primary',
      bgColor: 'grey-lighten-4',
    },
    VTextarea: {
      variant: 'solo-filled',
      flat: true,
      color: 'primary',
      bgColor: 'grey-lighten-4',
    }
  }
})
