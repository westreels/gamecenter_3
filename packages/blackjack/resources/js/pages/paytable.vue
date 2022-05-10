<template>
  <div class="paytable">
    <h5>
      {{ $t('Payout rules') }}
    </h5>
    <table>
      <thead>
        <tr>
          <th>{{ $t('Result') }}</th>
          <th>{{ $t('Payout') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $t('Blackjack') }}</td>
          <td>3:2</td>
        </tr>
        <tr>
          <td>{{ $t('Regular win') }}</td>
          <td>1:1</td>
        </tr>
        <tr>
          <td>{{ $t('Insurance win') }}</td>
          <td>2:1</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
