import {
  GAME_ADD,
  GAME_INCREMENT_COUNT
} from '../mutation-types'

import { get } from '~/plugins/utils'

const biggestWin = get(window, 'games.biggest_win')
const lastWin = get(window, 'games.last_win')

// state
export const state = {
  biggestWin,
  recent: lastWin ? [lastWin] : [], // recent games
  count: get(window, 'games.count', 0) // total number of games, is not equal to recent.length
}

// mutations
export const mutations = {
  [GAME_ADD] (state, payload) {
    state.recent.push(payload)
  },
  [GAME_INCREMENT_COUNT] (state) {
    state.count++
  }
}

// actions
export const actions = {
  add ({ commit }, payload) {
    commit(GAME_ADD, payload)
    commit(GAME_INCREMENT_COUNT)
  }
}
