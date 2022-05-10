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
              {{ $t('The objective of the game is to correctly guess which horse will finish in a place, i.e. finish first, second or third.') }}
              {{ $t('You can bet on several horses thus increasing your chances to win.') }}
            </p>
            <p>
              {{ $t('The following types of bets are supported:') }}
            </p>
            <ul>
              <li>
                {{ $t('Win — you win if your horse wins the race.') }}
              </li>
              <li>
                {{ $t('Place — you win if your horse finishes first or second.') }}
              </li>
              <li>
                {{ $t('Show — you win if your horse finishes first, second or third.') }}
              </li>
            </ul>
            <p></p>
            <p>
              {{ $t('Payouts are displayed to the right of the bet input field.') }}
              {{ $t('They differ depending on which bet and horse you choose.') }}
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
