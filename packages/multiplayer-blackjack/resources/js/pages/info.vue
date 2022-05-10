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
              {{ $t('The objective of the game is to get a hand total of closer to 21 than other players without going over 21.') }}
              {{ $t('The numeral cards 2 to 10 have their face values, Jacks, Queens and Kings are valued at 10, and Aces can have a value of either 1 or 11.') }}
              {{ $t('The Ace is always valued at 11 unless that would result in the hand going over 21, in which case it is valued as 1.') }}
            </p>
            <p>
              {{ $t('After joining the game room and placing a bet all players get 2 cards.') }}
              {{ $t('The first card is dealt face up and other players can see it.') }}
              {{ $t('Each player takes turn to either hit (get one or more cards) or stand.') }}
              {{ $t('Each player has {0} seconds to complete their turn.', [actionDuration]) }}
              {{ $t('When less than {0} seconds is left to complete the turn and the player chooses to hit they will get one card and automatically stand.', [finalHitThreshold]) }}
              {{ $t('Even if busted (going over 21 points) the player also needs to stand.') }}
              {{ $t('This makes it more difficult for the next player to guess how many points the previous player got.') }}
              {{ $t('The game end after all players stand.') }}
            </p>
            <p>
              {{ $t('The winner (the one who gets more points without going over 21) gets the pot size (all players bets) minus the house fee of {0}%.', [fee]) }}
              {{ $t('If several players get equal points they share the pot size.') }}
              {{ $t('When someone is dealt blackjack (an ace and a ten-value card) the game ends immediately.') }}
              {{ $t('If all players are busted (get more than 21 points) no one wins and the house keeps all bets.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      infoTab: 'tab-about'
    }
  },

  computed: {
    actionDuration () {
      return parseInt(config('multiplayer-blackjack.action_duration'), 10)
    },
    finalHitThreshold () {
      return parseInt(config('multiplayer-blackjack.final_hit_threshold'), 10)
    },
    fee () {
      return config('multiplayer-blackjack.fee')
    }
  }
}
</script>
