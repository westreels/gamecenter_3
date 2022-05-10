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
              {{ $t('The goal of the game is to predict which range the sum of points of all dice will fall into.') }}
              {{ $t('Scroll the slider to adjust the range.') }}
              {{ $t('Throw the dice by clicking "Play" button or holding the left mouse button and moving it in the direction of the throw.') }}
            </p>
            <p>
              {{ $t('The probability of winnings and win coefficient are in strict dependence of the winning chance and the width of the range.') }}
              {{ $t('It means that the probability of winning is higher when the range is wider and vice versa.') }}
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
