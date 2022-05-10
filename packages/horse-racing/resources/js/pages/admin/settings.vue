<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_HORSE_RACING_CATEGORIES"
              :label="$t('Categories')"
              multiple
              outlined
              chips
              small-chips
              deletable-chips
              hide-selected
              no-filter
            />

            <file-upload
              v-model="form.GAME_HORSE_RACING_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/horse-racing"
            />

            <file-upload
              v-model="form.GAME_HORSE_RACING_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/horse-racing"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_HORSE_RACING_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationNonNegativeNumber]"
              :error="form.errors.has('GAME_HORSE_RACING_MIN_BET')"
              :error-messages="form.errors.get('GAME_HORSE_RACING_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_HORSE_RACING_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_HORSE_RACING_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_HORSE_RACING_MAX_BET')"
              :error-messages="form.errors.get('GAME_HORSE_RACING_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_HORSE_RACING_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_HORSE_RACING_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_HORSE_RACING_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_HORSE_RACING_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_HORSE_RACING_BET_CHANGE_AMOUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Runners') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-card
              v-for="(runner, i) in form.GAME_HORSE_RACING_RUNNERS"
              :key="i"
              outlined
              class="mb-5"
            >
              <v-card-title class="font-weight-thin mb-3">
                {{ $t('Runner {0}', [i + 1]) }} &mdash; {{ runner.name }}
              </v-card-title>
              <v-card-text>
                <v-img :src="runnerImages[i]" width="250" height="200" />

                <v-text-field
                  v-model="form.GAME_HORSE_RACING_RUNNERS[i].name"
                  :label="$t('Name')"
                  :rules="[validationRequired]"
                  outlined
                />

                <v-row>
                  <v-col cols="12" md="6" lg="3">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.horse.body" :label="$t('Horse body')" @input="updateRunnerImage(i)" />
                  </v-col>
                  <v-col cols="12" md="6" lg="3">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.horse.tail" :label="$t('Horse tail')" @input="updateRunnerImage(i)" />
                  </v-col>
                  <v-col cols="12" md="6" lg="3">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.horse.pad.text" :label="$t('Pad text')" @input="updateRunnerImage(i)" />
                  </v-col>
                  <v-col cols="12" md="6" lg="3">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.horse.pad.background" :label="$t('Pad background')" @input="updateRunnerImage(i)" />
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" md="6">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.jockey.hat" :label="$t('Jockey hat')" @input="updateRunnerImage(i)" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <color-picker v-model="form.GAME_HORSE_RACING_RUNNERS[i].colors.jockey.shirt" :label="$t('Jockey shirt')" @input="updateRunnerImage(i)" />
                  </v-col>
                </v-row>

                <v-btn v-if="runnersCount >= 4 && runnersCount <= 8" color="error" @click="removeRunner(i)">
                  {{ $t('Remove') }}
                </v-btn>
              </v-card-text>
            </v-card>

            <v-row v-if="runnersCount <= 7">
              <v-col>
                <v-btn color="primary" @click="addRunner()">
                  {{ $t('Add') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(title, i) in betTypes">
              <h6 :key="`h-${i}`" class="text-h6 font-weight-thin">
                {{ title }}
              </h6>
              <v-row :key="`row-${i}`">
                <v-col v-for="(payout, k) in form.GAME_HORSE_RACING_PAYTABLE[i]" :key="k" :cols="cols">
                  <v-text-field
                    v-model.number="form.GAME_HORSE_RACING_PAYTABLE[i][k]"
                    :label="$t('Runner {0}', [k+1])"
                    :rules="[validationPositiveNumber]"
                    outlined
                    :prefix="$t('Bet') + ' x '"
                    @keydown="clearFormErrors($event, 'GAME_HORSE_RACING_PAYTABLE')"
                  />
                </v-col>
              </v-row>
            </template>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Animation') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.GAME_HORSE_RACING_ANIMATION_LENGTH"
              :label="$t('Animation duration')"
              :rules="[validationInteger, v => validationMin(v, 5), v => validationMax(v, 60)]"
              :error="form.errors.has('GAME_HORSE_RACING_ANIMATION_LENGTH')"
              :error-messages="form.errors.get('GAME_HORSE_RACING_ANIMATION_LENGTH')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_HORSE_RACING_ANIMATION_LENGTH')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import FileUpload from '~/components/Admin/FileUpload'
import ColorPicker from '~/components/ColorPicker'
import CRunner from '../helpers/runner'
import cloneDeep from 'clone-deep'

export default {
  components: { ColorPicker, FileUpload },

  mixins: [FormMixin],

  props: {
    form: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      betTypes: [
        this.$t('Win'),
        this.$t('Place'),
        this.$t('Show')
      ],
      runnerImages: [],
      runnerImagesT: {},
      variables: {
        GAME_HORSE_RACING_CATEGORIES: config('horse-racing.categories'),
        GAME_HORSE_RACING_BANNER: config('horse-racing.banner'),
        GAME_HORSE_RACING_BACKGROUND: config('horse-racing.background'),
        GAME_HORSE_RACING_MIN_BET: parseInt(config('horse-racing.min_bet'), 10),
        GAME_HORSE_RACING_MAX_BET: parseInt(config('horse-racing.max_bet'), 10),
        GAME_HORSE_RACING_BET_CHANGE_AMOUNT: parseInt(config('horse-racing.bet_change_amount'), 10),
        GAME_HORSE_RACING_RUNNERS: config('horse-racing.runners'),
        GAME_HORSE_RACING_PAYTABLE: config('horse-racing.paytable'),
        GAME_HORSE_RACING_ANIMATION_LENGTH: parseInt(config('horse-racing.animation.length'), 10)
      }
    }
  },

  computed: {
    cols () {
      const { xs, sm, md } = this.$vuetify.breakpoint

      return xs || sm ? 12 : md ? 6 : null
    },
    runnersCount () {
      return config('horse-racing.runners').length
    }
  },

  async created () {
    this.$emit('input', this.variables)

    const runners = this.variables.GAME_HORSE_RACING_RUNNERS
    for (const index in runners) {
      const image = await this.getRunnerImage(runners[index].colors, runners[index].name, index + 1)
      this.runnerImages.push(image)
    }
  },

  methods: {
    updateRunnerImage (index) {
      if (this.runnerImagesT[index]) clearTimeout(this.runnerImagesT[index])
      this.runnerImagesT[index] = setTimeout(async () => {
        this.runnerImagesT[index] = 0
        const runners = this.variables.GAME_HORSE_RACING_RUNNERS
        this.$set(this.runnerImages, index, await this.getRunnerImage(runners[index].colors, runners[index].name, index + 1))
      }, 500)
    },
    async getRunnerImage (colors, name, number) {
      const runner = new CRunner(colors, name, number)
      await runner.init()
      return runner.preview
    },
    async addRunner () {
      const runner = cloneDeep(this.form.GAME_HORSE_RACING_RUNNERS[this.runnersCount - 1])
      const n = this.runnersCount + 1
      runner.name = this.$t('Runner') + ' ' + n
      this.form.GAME_HORSE_RACING_RUNNERS.push(runner)
      const image = await this.getRunnerImage(runner.colors, runner.name, n)
      this.runnerImages.push(image)

      for (const betType in this.betTypes) {
        this.form.GAME_HORSE_RACING_PAYTABLE[betType].push(1)
      }
    },
    removeRunner (index) {
      this.form.GAME_HORSE_RACING_RUNNERS.splice(index, 1)
      this.runnerImages.splice(index, 1)

      for (const betType in this.betTypes) {
        this.form.GAME_HORSE_RACING_PAYTABLE[betType].splice(index, 1)
      }
    }
  }
}
</script>
