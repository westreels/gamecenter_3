<template>
  <v-container>
    <data-table
      api="/api/admin/chat/rooms"
      :title="$t('Chat rooms')"
      :headers="headers"
    >
      <template v-slot:table-prepend>
        <v-btn color="primary" :to="{ name: 'admin.chat.rooms.create' }" class="mb-5">
          {{ $t('Create chat room') }}
        </v-btn>
      </template>
      <template v-slot:item.status_title="{ item }">
        <span :class="item.enabled ? 'green--text' : 'error--text'">
          {{ item.status_title }}
        </span>
      </template>
      <template v-slot:item.actions="{ item }">
        <chat-room-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import ChatRoomMenu from '~/components/Admin/ChatRoomMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { ChatRoomMenu, DataTable },

  metaInfo () {
    return { title: this.$t('Chat rooms') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Status'), value: 'status_title', sortable: false },
        { text: this.$t('Messages count'), value: 'messages_count', align: 'right' },
        { text: this.$t('Created'), value: 'created_ago', align: 'right', sortable: false },
        { text: this.$t('Updated'), value: 'updated_ago', align: 'right', sortable: false },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
