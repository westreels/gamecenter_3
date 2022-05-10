<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Account {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <account-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="account"
              :api="`/api/admin/accounts/${id}`"
              :headers="headers"
            >
              <template v-slot:user.name="{ item: { user } }">
                <router-link :to="{ name: 'admin.users.show', params: { id: user.id } }">{{ user.name }}</router-link>
              </template>
            </key-value-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { KeyValueTable, AccountMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Account {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('User'), value: 'user.name' },
        { text: this.$t('Balance'), value: 'balance', format: 'decimal' },
        { text: this.$t('Created at'), value: 'created_at' },
        { text: this.$t('Updated at'), value: 'updated_at' }
      ]
    }
  }
}
</script>
