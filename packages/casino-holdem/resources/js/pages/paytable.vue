<template>
  <div class="paytable">
    <h5>
      {{ $t('Ante paytable') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>{{ $t('Hand') }}</th>
          <th class="text-right">
            {{ $t('Payout') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(hand, i) in ante.hands">
          <tr v-if="ante.paytable[i] > 0" :key="i">
            <td>{{ hand }}</td>
            <td class="text-right">
              {{ $t('bet') }} x {{ ante.paytable[i] }} ({{ ante.paytable[i] - 1 }}:1)
            </td>
          </tr>
        </template>
      </tbody>
    </table>
    <h5>
      {{ $t('Bonus paytable') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>{{ $t('Hand') }}</th>
          <th class="text-right">
            {{ $t('Payout') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(hand, i) in bonus.hands">
          <tr v-if="bonus.paytable[i] > 0" :key="i">
            <td>{{ hand }}</td>
            <td class="text-right">
              {{ $t('bet') }} x {{ bonus.paytable[i] }} ({{ bonus.paytable[i] - 1 }}:1)
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      ante: {
        paytable: config('casino-holdem.ante_paytable'),
        hands: [
          this.$t('High card'),
          this.$t('Pair'),
          this.$t('Two pair'),
          this.$t('Three of a kind'),
          this.$t('Straight'),
          this.$t('Flush'),
          this.$t('Full house'),
          this.$t('Four of a kind'),
          this.$t('Straight flush'),
          this.$t('Royal flush')
        ]
      },
      bonus: {
        paytable: config('casino-holdem.bonus_paytable'),
        hands: [
          this.$t('High card'),
          this.$t('Pair of aces'),
          this.$t('Two pair'),
          this.$t('Three of a kind'),
          this.$t('Straight'),
          this.$t('Flush'),
          this.$t('Full house'),
          this.$t('Four of a kind'),
          this.$t('Straight flush'),
          this.$t('Royal flush')
        ]
      }
    }
  }
}
</script>
