<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" lg="8">
        <v-card>
          <v-toolbar>
            <v-btn @click="$router.go(-1)" icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Game {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <slot name="menu" />
          </v-toolbar>
          <v-card-text>
            <v-simple-table v-if="game" dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">
                      {{ $t('Runner') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Bet type') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Bet') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Win') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, i) in game.gameable.bets" :key="i">
                    <td class="text-left">
                      <v-chip
                        v-if="runners[item.positions[0]]"
                        :color="runners[item.positions[0]].colors.horse.pad.background"
                        :text-color="runners[item.positions[0]].colors.horse.pad.text"
                        label
                        small
                        class="mr-1"
                      >
                        {{ item.positions[0] + 1 }}
                      </v-chip>
                      {{ runners[item.positions[0]] ? runners[item.positions[0]].name : $t('Runner {0}', [item.positions[0]]) }}
                    </td>
                    <td class="text-right">
                      {{ betTypes[item.type] }}
                    </td>
                    <td class="text-right">
                      {{ item.bet }}
                    </td>
                    <td class="text-right">
                      {{ item.win }}
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <key-value-table
              name="game"
              :api="`/api/history/games/${id}/details`"
              :headers="headers"
              class="mt-5"
              @load="game = $event.data"
            >
              <template v-slot:gameable.result="{ item: { gameable } }">
                <span v-for="index in gameable.positions" :key="index" class="ml-2">
                  <v-chip
                    v-if="runners[index]"
                    :color="runners[index].colors.horse.pad.background"
                    :text-color="runners[index].colors.horse.pad.text"
                    label
                    small
                    class="mr-1"
                  >
                    {{ index + 1 }}
                  </v-chip>
                  {{ runners[index] ? runners[index].name : $t('Runner {0}', [index] ) }}
                </span>
              </template>
            </key-value-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'
import KeyValueTable from '~/components/KeyValueTable'

export default {
  components: { KeyValueTable },

  props: ['id'],

  data () {
    return {
      betTypes: [
        this.$t('Win'),
        this.$t('Place'),
        this.$t('Show')
      ],
      game: null
    }
  },

  computed: {
    runners () {
      return config('horse-racing.runners')
    },
    headers () {
      return [
        { text: this.$t('Race result'), value: 'gameable.result' },
        { text: this.$t('Total bet'), value: 'gameable.bet_total', format: 'decimal' },
        { text: this.$t('Total win'), value: 'gameable.win_total', format: 'decimal' },
        { text: this.$t('Created at'), value: 'gameable.created_at' },
        { text: this.$t('Updated at'), value: 'gameable.updated_at' }
      ]
    }
  }
}
</script>
