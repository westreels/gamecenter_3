<template>
  <v-hover v-slot="{ hover }">
    <v-card :elevation="hover ? 12 : 2">
      <router-link :to="route" class="text-decoration-none">
        <v-img
          :src="game.banner"
          height="250px"
          class="game-banner align-center text-center"
          :class="{ hover }"
        >
          <template #default>
            <v-card-title class="justify-center">
              <v-chip color="rgba(0, 0, 0, 0.4)" label x-large pill text-color="white" class="font-weight-thin text-uppercase">
                {{ game.name }}
              </v-chip>
            </v-card-title>
          </template>
          <template #placeholder>
            <v-skeleton-loader type="image" class="fill-height" />
          </template>
        </v-img>
      </router-link>
      <v-card-subtitle v-if="game.provider">
        {{ game.provider }}
      </v-card-subtitle>
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

      .v-skeleton-loader {
        .v-skeleton-loader__image {
          height: 100%;
        }
      }
    }

    &.hover {
      &::v-deep {
        .v-responsive__sizer {
          background: rgba(0, 0, 0, 0.55) !important;
        }
      }
    }

    .v-chip {
      cursor: pointer;
    }
  }
}
</style>
