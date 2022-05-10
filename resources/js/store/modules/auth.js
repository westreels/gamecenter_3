import axios from 'axios'
import {
  AUTH_CLEAR_USER,
  AUTH_UPDATE_USER_ACCOUNT_BALANCE,
  AUTH_UPDATE_USER
} from '../mutation-types'

// state
export const state = {
  user: window.user,
  account: window.user ? window.user.account : null
}

// getters
export const getters = {
  check: state => state.user !== null
}

// mutations
export const mutations = {
  [AUTH_CLEAR_USER] (state) {
    state.user = null
    state.account = null
  },

  [AUTH_UPDATE_USER] (state, user) {
    state.user = user
    state.account = user.account
  },

  [AUTH_UPDATE_USER_ACCOUNT_BALANCE] (state, balance) {
    state.account.balance = balance
  }
}

// actions
export const actions = {
  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/api/user')
      
      commit(AUTH_UPDATE_USER, data)
    } catch (e) {
      //
    }
  },

  clearUser ({ commit }) {
    commit(AUTH_CLEAR_USER)
  },

  updateUser ({ commit }, payload) {
    commit(AUTH_UPDATE_USER, payload)
  },

  updateUserAccountBalance ({ commit }, balance) {
    commit(AUTH_UPDATE_USER_ACCOUNT_BALANCE, balance)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/api/auth/logout')
    } catch (e) {
      //
    }

    commit(AUTH_CLEAR_USER)
  },

  async fetchOAuthRedirectUrl (ctx, { provider }) {
    const { data } = await axios.post(`/api/oauth/${provider}/url`)

    return data.url
  }
}
