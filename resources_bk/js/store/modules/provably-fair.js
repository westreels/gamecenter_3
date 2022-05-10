import Vue from 'vue'
import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  games: {}
}

// getters
export const getters = {
  get: state => key => state.games[key] || null
}

// mutations
export const mutations = {
  [types.SET] (state, { key, game }) {
    if (state.games[key]) {
      state.games[key] = game
    } else {
      Vue.set(state.games, key, game)
    }
  }
}

// actions
export const actions = {
  set ({ commit }, { key, game }) {
    commit(types.SET, { key, game })
  },

  async create ({ commit }, { key, seed = null }) {
    const { data: game } = await axios.post('/api/user/games/create', {
      game_package_id: key,
      client_seed: seed || Math.ceil(Math.random() * 99999999)
    })

    commit(types.SET, { key, game })
  }
}
