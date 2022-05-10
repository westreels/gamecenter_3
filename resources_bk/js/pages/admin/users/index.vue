<template>
  <v-container>
    <data-table
      :title="$t('Users')"
      api="/api/admin/users"
      :headers="headers"
      :filters="['period', 'user-role', 'user-status']"
      :search="true"
      :search-placeholder="$t('ID, name or email')"
    >
      <template v-slot:item.name="{ item }">
        <user-link :user="item" />
      </template>
      <template v-slot:item.email="{ item }">
        <a :href="'mailto:' + item.email">{{ item.email }}</a>
        <v-tooltip v-if="item.email_verified_at" bottom>
          <template v-slot:activator="{ on }">
            <v-icon color="success" small v-on="on">
              mdi-check
            </v-icon>
          </template>
          <span>{{ $t('Email verified') }}</span>
        </v-tooltip>
      </template>
      <template v-slot:item.actions="{ item }">
        <user-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import UserMenu from '~/components/Admin/UserMenu'

export default {
  components: { DataTable, UserLink, UserMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Users') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Email'), value: 'email' },
        { text: this.$t('Signed up'), value: 'created_ago', align: 'right' },
        { text: this.$t('Last seen'), value: 'last_seen_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
