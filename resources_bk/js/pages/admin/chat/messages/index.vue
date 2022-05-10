<template>
  <v-container>
    <data-table
      api="/api/admin/chat/messages"
      :title="$t('Chat messages')"
      :headers="headers"
      :filters="['period']"
      :search="true"
      :search-placeholder="$t('Message ID or text, user name or email')"
    >
      <template v-slot:item.name="{ item: { user } }">
        <user-link :user="user" />
      </template>
      <template v-slot:item.actions="{ item }">
        <chat-message-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import ChatMessageMenu from '~/components/Admin/ChatMessageMenu'

export default {
  components: { ChatMessageMenu, DataTable, UserLink },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Chat messages') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Room'), value: 'room.name', sortable: false },
        { text: this.$t('Message'), value: 'message', sortable: false },
        { text: this.$t('Posted'), value: 'created_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
