<template>
  <div>
    <v-speed-dial
      v-model="menu"
      fixed
      bottom
      right
      open-on-hover
      direction="top"
      transition="scroll-y-transition"
      class="mb-5"
    >
      <template v-slot:activator>
        <v-btn v-model="menu" color="primary" fab>
          <v-icon v-if="menu">
            mdi-close
          </v-icon>
          <v-icon v-else>
            mdi-cards-playing-outline
          </v-icon>
        </v-btn>
      </template>
      <v-btn fab small color="primary" @click="infoModal = true">
        <v-icon>mdi-information-variant</v-icon>
      </v-btn>
    </v-speed-dial>

    <v-dialog v-model="infoModal" width="600" class="overflow-hidden">
      <v-card>
        <v-toolbar>
          <v-toolbar-title>
            {{ $t('Information') }}
          </v-toolbar-title>
          <v-spacer />
          <v-btn icon @click="infoModal = false">
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
                  {{ $t('A raffle is a competition in which people obtain numbered tickets.') }}
                  {{ $t('Each ticket has equal chance of winning a cash prize.') }}
                  {{ $t('The pot size grows based on the number of tickets sold.') }}
                  {{ $t('Each raffle displays current and max pot sizes.') }}
                  {{ $t('The max pot size is reached when all tickets are sold.') }}
                  {{ $t('The more tickets you buy the more chances you have to win.') }}
                </p>
                <p>
                  {{ $t('Some raffles end on a specific date and some raffles end when all tickets are sold.') }}
                  {{ $t('The completion clause is displayed in the bottom left corner of each raffle.') }}
                  {{ $t('The winner will be drawn as soon as the raffle completes.') }}
                </p>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import FormMixin from '~/mixins/Form'

export default {
  mixins: [FormMixin],

  data () {
    return {
      menu: false,
      infoModal: false,
      infoTab: 'tab-about'
    }
  }
}
</script>
