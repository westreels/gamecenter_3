<template>
  <v-menu :offset-y="true" transition="scroll-y-transition" bottom left>
    <template v-slot:activator="{ on }">
      <v-btn icon v-on="on">
        <v-icon :small="small">mdi-dots-vertical</v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item v-if="get(item, 'payment_method.gateway.code') === 'coinpayments'" :to="{ name: 'admin.withdrawal-methods.info', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-information-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Info') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item v-if="get(item, 'payment_method.gateway.code') === 'coinpayments'" :to="{ name: 'admin.withdrawal-methods.balance', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-wallet-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Balance') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.withdrawal-methods.edit', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-pencil</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Edit') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.withdrawal-methods.delete', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-delete</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Delete') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import { get } from '~/plugins/utils'

export default {
  props: ['id', 'item', 'small'],

  methods: {
    get
  }
}
</script>
