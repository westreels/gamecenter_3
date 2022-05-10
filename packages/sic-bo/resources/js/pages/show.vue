<template>
  <div>
    <key-value-table
      name="game"
      :api="`/api/history/games/${id}`"
      :headers="headers"
      @load="game = $event.data"
    >
      <template v-slot:account.user.name="{ item: { account : { user } } }">
        <user-profile-modal :user="user" />
      </template>
      <template v-slot:gameable.roll="{ item: { gameable } }">
        <v-icon v-for="(n, i) in gameable.roll" :key="i" large>
          mdi-dice-{{ n }}-outline
        </v-icon>
      </template>
    </key-value-table>
    <v-simple-table v-if="game" dense class="mt-5">
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">
              {{ $t('Bet type') }}
            </th>
            <th class="text-right">
              {{ $t('Bet') }}
            </th>
            <th class="text-right">
              {{ $t('Win') }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, i) in game.gameable.bets" :key="i">
            <td class="text-left">
              {{ betTypes[item.type] }}
              <template v-if="item.numbers.length">: {{ item.numbers.join(', ') }}</template>
            </td>
            <td class="text-right">
              {{ item.amount }}
            </td>
            <td class="text-right">
              {{ item.win }}
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  components: { UserProfileModal, KeyValueTable },

  props: ['id'],

  data () {
    return {
      betTypes: {
        small: this.$t('Small'),
        big: this.$t('Big'),
        single: this.$t('Single'),
        double: this.$t('Double'),
        triple: this.$t('Triple'),
        any_triple: this.$t('Any Triple'),
        combination: this.$t('Combination'),
        total: this.$t('3 Dice Total')
      },
      game: null
    }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Roll'), value: 'gameable.roll' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>
