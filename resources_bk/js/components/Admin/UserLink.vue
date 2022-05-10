<template>
  <span class="text-no-wrap">
    <user-avatar :user="user" />
    <router-link :to="{ name: 'admin.users.show', params: { id: user.id } }" class="mr-1">{{ user.name }}</router-link>
    <v-chip v-if="!user.is_active" color="error" small label>{{ $t('Blocked') }}</v-chip>
    <v-chip v-if="user.is_admin" color="primary" small outlined label>{{ $t('Admin') }}</v-chip>
    <v-chip v-else-if="user.is_bot" color="warning" small outlined label>{{ $t('Bot') }}</v-chip>
    <v-tooltip v-for="profile in user.profiles" :key="profile.id" bottom>
      <template v-slot:activator="{ on }">
        <v-icon small v-on="on">mdi-{{ providers[profile.provider_name].mdi || profile.provider_name }}</v-icon>
      </template>
      <span>{{ profile.provider_name }} {{ $t('profile ID') }} {{ profile.provider_user_id }}</span>
    </v-tooltip>
    <v-tooltip v-if="user.referrer" bottom>
      <template v-slot:activator="{ on }">
        <v-icon small v-on="on">mdi-account-arrow-left</v-icon>
      </template>
      <span>{{ $t('Referred by {0}', [user.referrer.name]) }}</span>
    </v-tooltip>
    <v-tooltip v-if="user.two_factor_auth_enabled" bottom>
      <template v-slot:activator="{ on }">
        <v-icon small v-on="on">mdi-two-factor-authentication</v-icon>
      </template>
      <span>{{ $t('Two-factor authentication enabled') }}</span>
    </v-tooltip>
  </span>
</template>

<script>
import { config } from '~/plugins/config'
import UserAvatar from '~/components/UserAvatar'

export default {
  name: 'UserLink',

  components: { UserAvatar },

  props: ['user'],

  computed: {
    providers () {
      return config('oauth')
    }
  }
}
</script>
