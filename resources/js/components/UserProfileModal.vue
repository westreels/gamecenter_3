<template>
  <v-dialog v-model="modal" width="600">
    <template v-slot:activator="{ on }">
      <slot :on="on">
        <span class="link" v-on="on">
          {{ user.name }}
        </span>
      </slot>
    </template>
    <user-profile :id="user.id" />
  </v-dialog>
</template>

<script>
import UserProfile from '~/components/UserProfile'

export default {
  components: { UserProfile },

  props: {
    user: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      modal: false
    }
  }
}
</script>

<style lang="scss" scoped>
  .link {
    color: var(--v-anchor-base);
  }
</style>
