<template>
  <v-hover v-slot="{ hover }">
    <v-card :elevation="hover ? 12 : 2">
      <router-link :to="route">
        <v-img
          :src="game.banner"
          height="150px"
          class="game-banner align-center text-center"
          :class="{ hover }"
        >
          <template #default>
            <v-btn
              fab
              color="primary"
              class="play-button"
              :class="{ hover }"
            >
              <v-icon color="grey lighten-5">
                mdi-play
              </v-icon>
            </v-btn>
          </template>
          <template #placeholder>
            <v-skeleton-loader type="image" />
          </template>
        </v-img>
      </router-link>
      <v-card-title>
        {{ game.name }}
      </v-card-title>
      <v-card-subtitle v-if="game.provider">
        {{ game.provider }}
      </v-card-subtitle>
      <v-card-text>
        <v-btn color="primary" :to="route">
          {{ $t('Play') }}
        </v-btn>
      </v-card-text>
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

    .play-button {
      transition: all 0.5s;
      opacity: 0;

      &.hover {
        opacity: 1;
      }
    }
  }
}
</style>
