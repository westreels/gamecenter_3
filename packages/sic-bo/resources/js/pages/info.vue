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
              {{ $t('Playing Sic Bo is relatively easy and the goal is to determine the falling dice value.') }}
              {{ $t('There are three dice in the play and you can bet on the result of one, two or all rolled dice.') }}
              {{ $t('Each bet will offer different payouts and multiple bets can be placed in one game.') }}
            </p>
            <p>
              <b>{{ $t('Small') }}</b> &mdash; {{ $t('The total score will be from 4 to 10 (inclusive) with the exception of a triple') }}
            </p>
            <p>
              <b>{{ $t('Big') }}</b> &mdash; {{ $t('The total score will be from 11 to 17 (inclusive) with the exception of a triple.') }}
            </p>
            <p>
              <b>{{ $t('Triple') }}</b> &mdash; {{ $t('A specific number will appear on all three dice.') }}
            </p>
            <p>
              <b>{{ $t('Any triple') }}</b> &mdash; {{ $t('Any of the triples will appear.') }}
            </p>
            <p>
              <b>{{ $t('Three dice total') }}</b> &mdash; {{ $t('The sum of all three dice will be equal to the given number.') }}
            </p>
            <p>
              <b>{{ $t('Dice combination') }}</b> &mdash; {{ $t('Two of the dice will show a specific combination of two different numbers (for example, a 3 and a 4).') }}
            </p>
            <p>
              <b>{{ $t('Single') }}</b> &mdash; {{ $t('The specific number 1, 2, 3, 4, 5, or 6 will appear on one, two, or all three dice.') }}
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
