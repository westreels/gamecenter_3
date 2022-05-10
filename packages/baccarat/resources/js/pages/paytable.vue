<template>
  <div class="paytable">
    <h5>
      {{ $t('Payout rules') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>
            {{ $t('Result') }}
          </th>
          <th>
            {{ $t('Payout') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $t('Player bet win') }}</td>
          <td>{{ playerBetPayout }}</td>
        </tr>
        <tr>
          <td>{{ $t('Tie bet win') }}</td>
          <td>{{ tieBetPayout }}</td>
        </tr>
        <tr>
          <td>{{ $t('Banker bet win') }}</td>
          <td>{{ bankerBetPayout }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      playerBetPayout: config('baccarat.payouts.player') - 1 + ':1',
      tieBetPayout: config('baccarat.payouts.tie') - 1 + ':1',
      bankerBetPayout: config('baccarat.payouts.banker') - 1 + ':1'
    }
  },
  computed: {
    paytable () {
      return this.config.paytable
    }
  }
}
</script>
