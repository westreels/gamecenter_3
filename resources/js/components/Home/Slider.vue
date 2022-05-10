<template>
  <v-carousel
    :cycle="slider.cycle && slidesCount > 1"
    :hide-delimiters="!slider.pagination || slidesCount === 1"
    :show-arrows="slider.navigation && slidesCount > 1"
    :interval="slider.interval * 1000"
    hide-delimiter-background
    :height="slider.height"
  >
    <v-carousel-item
      v-for="(slide, i) in slider.slides"
      :key="i"
    >
      <v-parallax :src="slide.image.url" :height="slider.height">
        <v-row align="center" justify="center" class="darken-60">
          <v-col class="text-center" cols="12">
            <h2 v-if="slide.title" class="display-2">
              {{ slide.title }}
            </h2>
            <h3 v-if="slide.subtitle" class="display-1 font-weight-thin mt-5">
              {{ slide.subtitle }}
            </h3>
            <div v-if="slide.link.title" class="mt-5">
              <v-btn color="primary" large :href="slide.link.url">
                {{ slide.link.title }}
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-parallax>
    </v-carousel-item>
  </v-carousel>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  computed: {
    slider () {
      return config('settings.content.home.slider')
    },
    slidesCount () {
      return this.slider ? this.slider.slides.length : 0
    }
  }
}
</script>
<style lang="scss" scoped>
.darken-60 {
  background: rgba(0, 0, 0, 0.6);
}
</style>
