<template>
  <v-container fluid>
    <data-table
      api="/api/admin/maintenance/failed-jobs"
      :title="$t('Failed jobs')"
      :headers="headers"
      :search="true"
    >
      <template v-slot:toolbar-prepend>
        <v-btn icon @click="$router.go(-1)">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
      </template>
      <template v-slot:item.exception="{ item: { exception } }">
        {{ exception.substring(0, exception.indexOf('Stack trace')) }}
      </template>
      <template v-slot:item.actions="{ item }">
        <v-dialog v-model="payloadModals[item.id]" max-width="100vw">
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
              <v-btn icon @click="payloadModals[item.id] = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4">
              <pre>{{ JSON.stringify(item.payload, null, 2) }}</pre>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-dialog v-model="exceptionModals[item.id]" max-width="100vw">
          <template v-slot:activator="{ on }">
            <slot :on="on">
              <v-btn small color="secondary" v-on="on">
                {{ $t('Exception') }}
              </v-btn>
            </slot>
          </template>
          <v-card>
            <v-toolbar>
              <v-toolbar-title>
                {{ $t('Exception') }}
              </v-toolbar-title>
              <v-spacer />
              <v-btn icon @click="exceptionModals[item.id] = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4">
              <pre>{{ item.exception }}</pre>
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
    return { title: this.$t('Failed jobs') }
  },

  data () {
    return {
      payloadModals: {},
      exceptionModals: {}
    }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Queue'), value: 'queue', sortable: false },
        { text: this.$t('Name'), value: 'payload.displayName', sortable: false },
        { text: this.$t('Exception'), value: 'exception', sortable: false },
        { text: this.$t('Failed'), value: 'failed_ago', align: 'right' },
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
