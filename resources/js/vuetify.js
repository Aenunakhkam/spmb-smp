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
    primary: '#1E3A8A', // Biru Premium
    'primary-darken-1': '#172554',
    secondary: '#D4AF37', // Kuning Emas
    'secondary-darken-1': '#B8860B',
    error: '#B00020',
    info: '#3B82F6',
    success: '#10B981',
    warning: '#F59E0B',
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
