<template>
  <v-menu :offset-y="true" transition="scroll-y-transition" bottom left>
    <template v-slot:activator="{ on }">
      <v-btn icon v-on="on">
        <v-icon :small="small">mdi-dots-vertical</v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item :to="{ name: 'admin.affiliate.commissions.show', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-eye</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('View') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.affiliate.commissions.approve', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-check</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Approve') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.affiliate.commissions.reject', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">mdi-cancel</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Reject') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  props: ['id', 'small']
}
</script>
