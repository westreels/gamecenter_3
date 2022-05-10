<template>
  <div class="paytable">
    <h5>
      {{ $t('Payout rules') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>
            {{ $t('Dealer qualifies') }}
          </th>
          <th>
            {{ $t('Result') }}
          </th>
          <th>
            {{ $t('Ante win') }}
          </th>
          <th>
            {{ $t('Call win') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $t('No') }}</td>
          <td>{{ $t('Player wins') }}</td>
          <td>1:1</td>
          <td>{{ $t('Push') }}</td>
        </tr>
        <tr>
          <td>{{ $t('Yes') }}</td>
          <td>{{ $t('Player wins') }}</td>
          <td>1:1</td>
          <td>{{ $t('Paytable') }}</td>
        </tr>
        <tr>
          <td>{{ $t('Yes') }}</td>
          <td>{{ $t('Push') }}</td>
          <td>{{ $t('Push') }}</td>
          <td>{{ $t('Push') }}</td>
        </tr>
        <tr>
          <td>{{ $t('Yes') }}</td>
          <td>{{ $t('Dealer wins') }}</td>
          <td>&mdash;</td>
          <td>&mdash;</td>
        </tr>
      </tbody>
    </table>
    <h5>
      {{ $t('Call paytable') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>{{ $t('Hand') }}</th>
          <th>
            {{ $t('Payout') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(hand, i) in hands">
          <tr :key="i">
            <td>{{ hand }}</td>
            <td>
              {{ $t('bet') }} x {{ paytable[i] }} ({{ paytable[i] - 1 }}:1)
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
import GameMixin from '~/mixins/Game'

export default {
  mixins: [GameMixin],

  data () {
    return {
      infoTab: 'tab-about',
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
    }
  },

  computed: {
    paytable () {
      return this.config.paytable
    }
  }
}
</script>
