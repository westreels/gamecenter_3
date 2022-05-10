import { config } from './config'

const mode = config('settings.theme.mode')

export default {
  dark: mode === 'dark',
  options: {
    customProperties: true
  },
  themes: {
    [mode]: config('settings.theme.colors')
  }
}
