<template>
  <v-card>
    <v-toolbar>
      <v-toolbar-title>
        {{ $t('Game information') }}
      </v-toolbar-title>
      <v-spacer />
      <v-btn icon @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <template v-slot:extension>
        <v-tabs v-model="infoTab" centered hide-slider>
          <v-tab href="#tab-about">
            {{ $t('How to play') }}
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('Plinko is a game of chance played with a ball falling down the vertical board in the form of a pyramid.') }}
              {{ $t('After you place a bet the ball will start bouncing through the obstacles from the top of the pyramid to the bottom.') }}
              {{ $t('At the end it will drop into a slot (bucket) with the corresponding payout.') }}
              {{ $t('The closer the ball ends up along the sides the bigger your win will be.') }}
              {{ $t('Trust your luck and let the ball fall!') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      infoTab: 'tab-about'
    }
  }
}
</script>
