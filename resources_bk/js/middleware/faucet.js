import { config } from '~/plugins/config'

export default async (to, from, next) => {
  if (parseInt(config('settings.bonuses.faucet.amount'), 10) === 0) {
    next({ name: '404' })
  } else {
    next()
  }
}
