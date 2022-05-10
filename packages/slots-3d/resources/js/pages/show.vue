<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.positions="{ item: { gameable: { positions } } }">
      <v-avatar v-for="(reelSymbolIndex, reelIndex) in positions" :key="reelIndex" size="32">
        <img v-if="typeof symbols[reels[reelIndex][reelSymbolIndex]] !== 'undefined'" :src="symbols[reels[reelIndex][reelSymbolIndex]]">
        <span v-else class="font-weight-bold">&mdash;</span>
      </v-avatar>
    </template>
  </key-value-table>
</template>

<script>
import { config } from '~/plugins/config'
import KeyValueTable from '~/components/KeyValueTable'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  components: { UserProfileModal, KeyValueTable },

  props: ['id'],

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Symbols'), value: 'gameable.positions' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    },
    symbols () {
      return config('slots-3d.symbols')
    },
    reels () {
      return config('slots-3d.reels')
    }
  }
}
</script>
