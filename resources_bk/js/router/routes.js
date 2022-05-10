import HomePage from '~/pages' // dynamic import of the Home component doesn't work and produces ".js" file
import Callback from '~/pages/callback'

const packageRaffleId = 'raffle'
const packagePaymentsId = 'payments'

function page (path) {
  return () => import(/* webpackChunkName: '[request]' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  // home page
  { path: '/', name: 'home', component: HomePage },
  { path: '/callback', name: 'callback', component: Callback },

  // authentication
  { path: '/login', name: 'login', component: page('login'), meta: { layout: 'auth' } },
  { path: '/2fa', name: '2fa', component: page('2fa'), meta: { layout: 'auth' } },
  { path: '/register', name: 'register', component: page('register'), meta: { layout: 'auth' } },
  { path: '/password/reset', name: 'password.email', component: page('password/email'), meta: { layout: 'auth' } },
  { path: '/password/reset/:token', name: 'password.reset', component: page('password/reset'), meta: { layout: 'auth' } },
  { path: '/email', name: 'verification.index', component: page('email') },
  { path: '/email/verify/:id/:hash', name: 'verification.verify', component: page('email/verify') },

  // games
  { path: '/games/:game/:slug?', name: 'game', component: page('games') },

  // predictions
  { path: '/markets/:packageId', name: 'prediction', component: page('predictions') },

  // raffles
  {
    path: '/raffles',
    name: 'raffles',
    redirect: { name: 'raffles.current' },
    component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/raffles`),
    children: [
      { path: 'current', name: 'raffles.current', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/raffles/current`) },
      { path: 'completed', name: 'raffles.completed', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/raffles/completed`) }
    ]
  },


  // users
  { path: '/users/:id', name: 'users.show', component: page('users/show'), props: true },

  // user
  { path: '/user/edit', name: 'user.edit', component: page('user/edit') },

  {
    path: '/user/security',
    name: 'user.security',
    redirect: { name: 'user.security.password' },
    component: page('user/security'),
    children: [
      { path: 'password', name: 'user.security.password', component: page('user/security/password') },
      { path: '2fa', name: 'user.security.2fa', component: page('user/security/2fa') }
    ]
  },

  {
    path: '/user/affiliate',
    name: 'user.affiliate',
    redirect: { name: 'user.affiliate.info' },
    component: page('user/affiliate'),
    children: [
      { path: 'info', name: 'user.affiliate.info', component: page('user/affiliate/info') },
      { path: 'commissions', name: 'user.affiliate.commissions', component: page('user/affiliate/commissions') },
      { path: 'stats', name: 'user.affiliate.stats', component: page('user/affiliate/stats') }
    ]
  },

  { path: '/user/account/transactions', name: 'user.account.transactions', component: page('user/account/transactions') },
  { path: '/user/account/faucet', name: 'user.account.faucet', component: page('user/account/faucet') },

  { path: '/user/account/deposits', name: 'user.account.deposits.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/user/account/deposits`) },
  // { path: '/user/account/deposits/create', name: 'user.account.deposits.create', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/user/account/deposits/create`) },
  { path: '/user/account/deposits/:id/complete', name: 'user.account.deposits.complete', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/user/account/deposits/complete`), props: true },

  { path: '/user/account/withdrawals', name: 'user.account.withdrawals.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/user/account/withdrawals`) },
  // { path: '/user/account/withdrawals/create', name: 'user.account.withdrawals.create', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/user/account/withdrawals/create`) },

  // games history
  {
    path: '/history',
    name: 'history',
    redirect: { name: 'history.user' },
    component: page('history'),
    children: [
      { path: 'user', name: 'history.user', component: page('history/user') },
      { path: 'recent', name: 'history.recent', component: page('history/recent') },
      { path: 'wins', name: 'history.wins', component: page('history/wins') },
      { path: 'losses', name: 'history.losses', component: page('history/losses') },
      { path: 'games/:id', name: 'history.games.show', component: page('history/games/show'), props: true },
      { path: 'games/:id/verify', name: 'history.games.verify', component: page('history/games/verify'), props: true }
    ]
  },

  // leaderboard
  { path: '/leaderboard', name: 'leaderboard', component: page('leaderboard') },

  /*
   * BACKEND
   */
  // dashboard
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard.index',
    redirect: { name: 'admin.dashboard.users' },
    component: page('admin/dashboard'),
    children: [
      { path: 'users', name: 'admin.dashboard.users', component: page('admin/dashboard/users') },
      { path: 'affiliates', name: 'admin.dashboard.affiliates', component: page('admin/dashboard/affiliates') },
      { path: 'games', name: 'admin.dashboard.games', component: page('admin/dashboard/games') },
      { path: 'accounting', name: 'admin.dashboard.accounting', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/dashboard/accounting`) }
    ]
  },

  { path: '/admin/users', name: 'admin.users.index', component: page('admin/users/index') },
  { path: '/admin/users/:id', name: 'admin.users.show', component: page('admin/users/show'), props: true },
  { path: '/admin/users/:id/edit', name: 'admin.users.edit', component: page('admin/users/edit'), props: true },
  { path: '/admin/users/:id/delete', name: 'admin.users.delete', component: page('admin/users/delete'), props: true },
  { path: '/admin/users/:id/mail', name: 'admin.users.mail', component: page('admin/users/mail'), props: true },

  { path: '/admin/accounts', name: 'admin.accounts.index', component: page('admin/accounts/index') },
  { path: '/admin/accounts/:id', name: 'admin.accounts.show', component: page('admin/accounts/show'), props: true },
  { path: '/admin/accounts/:id/debit', name: 'admin.accounts.debit', component: page('admin/accounts/debit'), props: true },
  { path: '/admin/accounts/:id/credit', name: 'admin.accounts.credit', component: page('admin/accounts/credit'), props: true },
  { path: '/admin/accounts/:id/transactions', name: 'admin.accounts.transactions', component: page('admin/accounts/transactions'), props: true },

  { path: '/admin/raffles', name: 'admin.raffles.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/admin/raffles`) },
  { path: '/admin/raffles/create', name: 'admin.raffles.create', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/admin/raffles/create`) },
  { path: '/admin/raffles/:id/edit', name: 'admin.raffles.edit', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/admin/raffles/edit`), props: true },
  { path: '/admin/raffles/:id/complete', name: 'admin.raffles.complete', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/admin/raffles/complete`), props: true },
  { path: '/admin/raffles/:id/tickets', name: 'admin.raffles.tickets', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageRaffleId}/resources/js/pages/admin/raffles/tickets`), props: true },

  { path: '/admin/deposits', name: 'admin.deposits.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposits`) },
  { path: '/admin/deposits/:id', name: 'admin.deposits.show', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposits/show`), props: true },
  { path: '/admin/deposits/:id/transaction', name: 'admin.deposits.transaction', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposits/transaction`), props: true },

  { path: '/admin/withdrawals', name: 'admin.withdrawals.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawals`) },
  { path: '/admin/withdrawals/:id', name: 'admin.withdrawals.show', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawals/show`), props: true },
  { path: '/admin/withdrawals/:id/transaction', name: 'admin.withdrawals.transaction', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawals/transaction`), props: true },

  { path: '/admin/withdrawal-methods', name: 'admin.withdrawal-methods.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawal-methods`) },
  { path: '/admin/withdrawal-methods/create', name: 'admin.withdrawal-methods.create', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawal-methods/create`), props: true },
  { path: '/admin/withdrawal-methods/:id/info', name: 'admin.withdrawal-methods.info', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-withdrawal-methods/info`), props: true, meta: { type: 'withdrawal' } },
  { path: '/admin/withdrawal-methods/:id/balance', name: 'admin.withdrawal-methods.balance', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-withdrawal-methods/balance`), props: true, meta: { type: 'withdrawal' } },
  { path: '/admin/withdrawal-methods/:id/edit', name: 'admin.withdrawal-methods.edit', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawal-methods/edit`), props: true },
  { path: '/admin/withdrawal-methods/:id/delete', name: 'admin.withdrawal-methods.delete', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/withdrawal-methods/delete`), props: true },

  { path: '/admin/deposit-methods', name: 'admin.deposit-methods.index', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-methods`) },
  { path: '/admin/deposit-methods/create', name: 'admin.deposit-methods.create', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-methods/create`), props: true },
  { path: '/admin/deposit-methods/:id/info', name: 'admin.deposit-methods.info', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-withdrawal-methods/info`), props: true, meta: { type: 'deposit' } },
  { path: '/admin/deposit-methods/:id/balance', name: 'admin.deposit-methods.balance', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-withdrawal-methods/balance`), props: true, meta: { type: 'deposit' } },
  { path: '/admin/deposit-methods/:id/edit', name: 'admin.deposit-methods.edit', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-methods/edit`), props: true },
  { path: '/admin/deposit-methods/:id/delete', name: 'admin.deposit-methods.delete', component: () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${packagePaymentsId}/resources/js/pages/admin/deposit-methods/delete`), props: true },

  { path: '/admin/games', name: 'admin.games.index', component: page('admin/games/index') },
  { path: '/admin/games/:id', name: 'admin.games.show', component: page('admin/games/show'), props: true },

  {
    path: '/admin/affiliate',
    name: 'admin.affiliate.index',
    redirect: { name: 'admin.affiliate.tree' },
    component: page('admin/affiliate'),
    children: [
      { path: 'tree', name: 'admin.affiliate.tree', component: page('admin/affiliate/tree') },
      { path: 'commissions', name: 'admin.affiliate.commissions.index', component: page('admin/affiliate/commissions') },
      { path: 'commissions/:id', name: 'admin.affiliate.commissions.show', component: page('admin/affiliate/commissions/show'), props: true },
      { path: 'commissions/:id/reject', name: 'admin.affiliate.commissions.reject', component: page('admin/affiliate/commissions/reject'), props: true },
      { path: 'commissions/:id/approve', name: 'admin.affiliate.commissions.approve', component: page('admin/affiliate/commissions/approve'), props: true }
    ]
  },
  {
    path: '/admin/chat',
    name: 'admin.chat.index',
    redirect: { name: 'admin.chat.rooms.index' },
    component: page('admin/chat'),
    children: [
      { path: 'rooms', name: 'admin.chat.rooms.index', component: page('admin/chat/rooms') },
      { path: 'rooms/create', name: 'admin.chat.rooms.create', component: page('admin/chat/rooms/create') },
      { path: 'rooms/:id/edit', name: 'admin.chat.rooms.edit', component: page('admin/chat/rooms/edit'), props: true },
      { path: 'rooms/:id/delete', name: 'admin.chat.rooms.delete', component: page('admin/chat/rooms/delete'), props: true },
      { path: 'messages', name: 'admin.chat.messages.index', component: page('admin/chat/messages') },
      { path: 'messages/:id/delete', name: 'admin.chat.messages.delete', component: page('admin/chat/messages/delete'), props: true }
    ]
  },

  { path: '/admin/settings', name: 'admin.settings.index', component: page('admin/settings') },

  { path: '/admin/maintenance', name: 'admin.maintenance.index', component: page('admin/maintenance') },
  { path: '/admin/maintenance/jobs', name: 'admin.maintenance.jobs.index', component: page('admin/maintenance/jobs') },
  { path: '/admin/maintenance/failed-jobs', name: 'admin.maintenance.failed-jobs.index', component: page('admin/maintenance/failed-jobs') },
  { path: '/admin/maintenance/logs/:file', name: 'admin.maintenance.logs.show', component: page('admin/maintenance/logs/show'), props: true },

  { path: '/admin/add-ons', name: 'admin.add-ons.index', component: page('admin/add-ons') },
  { path: '/admin/add-ons/:id/install', name: 'admin.add-ons.install', component: page('admin/add-ons/install'), props: true },
  { path: '/admin/add-ons/:id/changelog', name: 'admin.add-ons.changelog', component: page('admin/add-ons/changelog'), props: true },

  { path: '/admin/license', name: 'admin.license.index', component: page('admin/license') },

  { path: '/admin/help/:file?', name: 'admin.help.index', component: page('admin/help'), props: true },

  { path: '/admin/tests/poker', name: 'admin.tests.poker', component: page('admin/tests/poker') },

  // static pages
  { path: '/pages/:id', name: 'page', component: page('pages'), props: true },

  // error page
  { path: '/offline', name: 'offline', component: page('offline') },
  { path: '/503', name: '503', component: page('503') },
  { path: '*', name: '404', component: page('404') }
]
