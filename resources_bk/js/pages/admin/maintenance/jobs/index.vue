<template>
  <v-container fluid>
    <data-table
      api="/api/admin/maintenance/jobs"
      :title="$t('Jobs')"
      :headers="headers"
      :search="true"
    >
      <template v-slot:toolbar-prepend>
        <v-btn icon @click="$router.go(-1)">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-dialog v-model="modals[item.id]" max-width="100vw">
          <template v-slot:activator="{ on }">
            <slot :on="on">
              <v-btn small color="secondary" v-on="on">
                {{ $t('Payload') }}
              </v-btn>
            </slot>
          </template>
          <v-card>
            <v-toolbar>
              <v-toolbar-title>
                {{ $t('Payload') }}
              </v-toolbar-title>
              <v-spacer />
              <v-btn icon @click="modals[item.id] = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4">
              <pre>{{ JSON.stringify(item.payload, null, 2) }}</pre>
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'

export default {
  components: { DataTable },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Jobs') }
  },

  data () {
    return {
      modals: {}
    }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Queue'), value: 'queue', sortable: false },
        { text: this.$t('Name'), value: 'payload.displayName', sortable: false },
        { text: this.$t('Attempts'), value: 'attempts', sortable: false },
        { text: this.$t('Created'), value: 'created_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
<style lang="scss" scoped>
pre {
  overflow-x: scroll;
}
</style>
