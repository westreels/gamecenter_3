<template>
  <v-menu :offset-y="true" transition="scroll-y-transition" bottom left>
    <template v-slot:activator="{ on }">
      <v-btn icon v-on="on">
        <v-icon :small="small">
          mdi-dots-vertical
        </v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item :to="{ name: 'admin.raffles.edit', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">
            mdi-pencil
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Edit') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.raffles.complete', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">
            mdi-check-bold
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Complete') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'admin.raffles.tickets', params: { id } }" exact>
        <v-list-item-icon>
          <v-icon :small="small">
            mdi-format-list-bulleted
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Tickets') }}</v-list-item-title>
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
