<template>
  <v-hover v-slot="{ hover }">
    <v-card :elevation="hover ? 12 : 2" width="250px" height="250px">
      <router-link :to="route" class="text-decoration-none">
        <v-img
          :src="game.banner"
          class="game-banner align-center text-center fill-height"
          :class="{ hover }"
        >
          <template #default>
            <v-card-title class="flex-column justify-space-around fill-height" :class="{ 'd-none': !hover }">
              <div>{{ game.name }}</div>
              <v-icon x-large>
                mdi-play-circle-outline
              </v-icon>
              <div>{{ game.provider }}</div>
            </v-card-title>
          </template>
          <template #placeholder>
            <v-skeleton-loader type="image" class="fill-height" />
          </template>
        </v-img>
      </router-link>
    </v-card>
  </v-hover>
</template>
<script>
export default {
  props: {
    game: {
      type: Object,
      required: true
    }
  },

  computed: {
    route () {
      return this.game.provider
        ? { name: 'provider.game', params: { provider: this.game.provider, game: this.game.id } }
        : { name: 'game', params: this.game.slug ? { game: this.game.id, slug: this.game.slug } : { game: this.game.id } }
    }
  }
}
</script>
<style lang="scss" scoped>
.game-banner {
  &.v-image {
    &::v-deep {
      .v-responsive__sizer {
        height: 100% !important;
        transition: all 0.5s;
        background: rgba(0, 0, 0, 0);
      }

      .v-responsive__content {
        height: 100%;
      }

      .v-skeleton-loader {
        .v-skeleton-loader__image {
          height: 100%;
        }
      }
    }

    &.hover {
      &::v-deep {
        .v-responsive__sizer {
          background: rgba(0, 0, 0, 0.8) !important;
        }
      }
    }
  }
}
</style>
