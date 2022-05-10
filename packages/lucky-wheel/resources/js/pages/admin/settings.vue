<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel v-for="(variation, variationIndex) in variations" :key="variationIndex">
          <v-expansion-panel-header>{{ variation.title }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-text-field
                    v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].title"
                    :label="$t('Title')"
                    :rules="[validationRequired]"
                    outlined
                  />

                  <v-text-field
                    v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].slug"
                    :label="$t('Slug')"
                    :rules="[validationRequired]"
                    :prefix="url"
                    outlined
                  />

                  <v-combobox
                    v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].categories"
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
                    v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].banner"
                    :label="$t('Banner')"
                    name="banner"
                    :folder="`games/lucky-wheel/${variationIndex}`"
                  />

                  <file-upload
                    v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].background"
                    :label="$t('Background image')"
                    name="background"
                    :folder="`games/lucky-wheel/${variationIndex}`"
                    :clearable="true"
                  />

                  <v-text-field
                    v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].min_bet"
                    :label="$t('Min bet')"
                    :rules="[validationInteger, validationPositiveNumber]"
                    outlined
                    :suffix="$t('credits')"
                  />

                  <v-text-field
                    v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].max_bet"
                    :label="$t('Max bet')"
                    :rules="[validationInteger, validationPositiveNumber]"
                    outlined
                    :suffix="$t('credits')"
                  />

                  <v-text-field
                    v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].bet_change_amount"
                    :label="$t('Bet increment / decrement amount')"
                    :rules="[validationInteger, validationPositiveNumber]"
                    outlined
                    :suffix="$t('credits')"
                  />

                  <v-text-field
                    v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].default_bet_amount"
                    :label="$t('Default bet amount')"
                    :rules="[validationInteger, validationPositiveNumber]"
                    outlined
                    :suffix="$t('credits')"
                  />
                </v-expansion-panel-content>
              </v-expansion-panel>
              <v-expansion-panel>
                <v-expansion-panel-header>{{ $t('Sections') }}</v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-card
                    v-for="(section, sectionIndex) in form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections"
                    :key="sectionIndex"
                    outlined
                    class="mb-5"
                  >
                    <v-card-title class="font-weight-thin mb-3">
                      {{ $t('Section {0}', [sectionIndex + 1]) }}
                    </v-card-title>
                    <v-card-text>
                      <v-text-field
                        v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections[sectionIndex].title"
                        :label="$t('Title')"
                        :rules="[validationRequired]"
                        outlined
                      />

                      <v-text-field
                        v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections[sectionIndex].payout"
                        :label="$t('Payout')"
                        :rules="[validationNumeric]"
                        prefix="x"
                        outlined
                      />

                      <color-picker v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections[sectionIndex].backgroundColor" :label="$t('Background color')" />

                      <color-picker v-model="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections[sectionIndex].fontColor" :label="$t('Font color')" />

                      <v-text-field
                        v-model.number="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections[sectionIndex].font"
                        :label="$t('Font size')"
                        :rules="[validationInteger, validationPositiveNumber]"
                        outlined
                      />

                      <v-btn v-if="form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections.length > 1" color="error" @click="removeSection(variationIndex, sectionIndex)">
                        {{ $t('Remove section') }}
                      </v-btn>
                    </v-card-text>
                  </v-card>

                  <v-row>
                    <v-col>
                      <v-btn color="primary" @click="addSection(variationIndex)">
                        {{ $t('Add section') }}
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>

            <v-row class="mt-5">
              <v-col>
                <v-btn v-if="variationsCount > 1 && variationIndex < variationsCount - 1" color="primary" icon large tile @click="moveVariationDown(variationIndex)">
                  <v-icon>mdi-arrow-down</v-icon>
                </v-btn>
                <v-btn v-if="variationsCount > 1 && variationIndex > 0" color="primary" icon large tile @click="moveVariationUp(variationIndex)">
                  <v-icon>mdi-arrow-up</v-icon>
                </v-btn>
                <v-btn color="primary" class="mx-2" @click="cloneVariation(variationIndex)">
                  {{ $t('Clone') }}
                </v-btn>
                <v-btn v-if="variationsCount > 1" color="error" @click="removeVariation(variationIndex)">
                  {{ $t('Remove') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import { moveArrayElement } from '~/plugins/utils'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import FileUpload from '~/components/Admin/FileUpload'
import ColorPicker from '~/components/ColorPicker'

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
      imageUploadForm: new Form({
        image: null
      }),
      cloneVariationForm: new Form({
        variation: null
      }),
      removeVariationForm: new Form()
    }
  },

  computed: {
    variations () {
      return config('lucky-wheel.variations')
    },
    variationsCount () {
      return this.variations.length
    },
    url () {
      return window.location.origin + '/lucky-wheel/'
    },
    theme () {
      const mode = this.$vuetify.theme.isDark ? 'dark' : 'light'
      return this.$vuetify.theme.themes[mode]
    }
  },

  created () {
    this.$emit('input', { GAME_LUCKY_WHEEL_VARIATIONS: this.variations })
  },

  methods: {
    moveVariationUp (variationIndex) {
      moveArrayElement(this.form.GAME_LUCKY_WHEEL_VARIATIONS, variationIndex, variationIndex - 1)
    },
    moveVariationDown (variationIndex) {
      moveArrayElement(this.form.GAME_LUCKY_WHEEL_VARIATIONS, variationIndex, variationIndex + 1)
    },
    async cloneVariation (variationIndex) {
      const n = this.form.GAME_LUCKY_WHEEL_VARIATIONS.length
      this.form.GAME_LUCKY_WHEEL_VARIATIONS.push(JSON.parse(JSON.stringify(this.form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex])))
      this.form.GAME_LUCKY_WHEEL_VARIATIONS[n].title += ' - cloned'
      this.form.GAME_LUCKY_WHEEL_VARIATIONS[n].slug += '-cloned'
    },
    async removeVariation (variationIndex) {
      this.form.GAME_LUCKY_WHEEL_VARIATIONS.splice(variationIndex, 1)
    },
    addSection (variationIndex) {
      this.form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections.push({
        title: null,
        payout: null,
        backgroundColor: this.theme.primary,
        fontColor: '#FFFFFF',
        font: 100
      })
    },
    removeSection (variationIndex, symbolIndex) {
      this.form.GAME_LUCKY_WHEEL_VARIATIONS[variationIndex].sections.splice(symbolIndex, 1)
    }
  }
}
</script>
