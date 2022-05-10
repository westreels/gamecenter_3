<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_SLOTS_3D_CATEGORIES"
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
              v-model="form.GAME_SLOTS_3D_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/slots-3d"
            />

            <file-upload
              v-model="form.GAME_SLOTS_3D_TEXTURE"
              :label="$t('Slot machine texture')"
              name="texture"
              folder="games/slots-3d"
            />

            <file-upload
              v-model="form.GAME_SLOTS_3D_BACKGROUND_SKYBOX"
              :label="$t('Panoramic background image')"
              name="background-skybox"
              folder="games/slots-3d"
            />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_BACKGROUND_SKYBOX_ROTATE"
              :label="$t('Y rotate angle')"
              :rules="[validationInteger]"
              outlined
            />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_BACKGROUND_SKYBOX_DEEP"
              :label="$t('Z axis offset')"
              :rules="[validationInteger]"
              outlined
            />

            <file-upload
              v-model="form.GAME_SLOTS_3D_TABLE_TEXTURE"
              :label="$t('Table texture')"
              name="table-texture"
              folder="games/slots-3d"
            />

            <color-picker v-model="form.GAME_SLOTS_3D_TABLE_COLOR" :label="$t('Table overlay color')" />

            <color-picker v-model="form.GAME_SLOTS_3D_TABLE_BORDER_COLOR" :label="$t('Table border color')" />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              outlined
              :suffix="$t('credits')"
            />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              outlined
              :suffix="$t('credits')"
            />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              outlined
              :suffix="$t('credits')"
            />

            <v-text-field
              v-model.number="form.GAME_SLOTS_3D_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              outlined
              :suffix="$t('credits')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Symbols') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(symbol, symbolIndex) in form.GAME_SLOTS_3D_SYMBOLS">
              <div :key="symbolIndex">
                <file-upload
                  v-model="form.GAME_SLOTS_3D_SYMBOLS[symbolIndex]"
                  :label="$t('Image')"
                  :name="`symbol-${symbolIndex}`"
                  folder="games/slots-3d"
                >
                  <template v-if="form.GAME_SLOTS_3D_SYMBOLS.length > 1" v-slot:append-outer>
                    <v-icon @click="removeSymbol(symbolIndex)">
                      mdi-delete
                    </v-icon>
                  </template>
                </file-upload>
              </div>
            </template>
            <v-row>
              <v-col>
                <v-btn color="primary" @click="addSymbol()">
                  {{ $t('Add symbol') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Reels') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-row>
              <v-col v-for="reelIndex in [0,1,2]" :key="reelIndex">
                <draggable v-if="form.GAME_SLOTS_3D_REELS" :list="form.GAME_SLOTS_3D_REELS[reelIndex]">
                  <div v-for="(symbolIndex, k) in form.GAME_SLOTS_3D_REELS[reelIndex]" :key="k" class="text-center mb-3">
                    <v-badge color="rgba(255,255,255,0)">
                      <v-btn
                        slot="badge"
                        color="error"
                        icon
                        x-small
                        @click="removeSymbolFromReel(reelIndex, k)"
                      >
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                      <v-avatar size="64" tile>
                        <img :src="symbolImagePath(symbolIndex)" :alt="symbolImagePath(symbolIndex)">
                      </v-avatar>
                    </v-badge>
                  </div>
                </draggable>
                <div class="text-center mt-5">
                  <v-menu
                    offset-y
                    top
                    right
                    max-height="300"
                  >
                    <template v-slot:activator="{ on }">
                      <v-btn color="primary" small v-on="on">
                        <v-icon>mdi-plus</v-icon>
                        {{ $t('Add') }}
                      </v-btn>
                    </template>

                    <v-list>
                      <v-list-item
                        v-for="(symbol, symbolIndex) in form.GAME_SLOTS_3D_SYMBOLS"
                        :key="symbolIndex"
                        @click="addSymbolToReel(reelIndex, symbolIndex)"
                      >
                        <v-list-item-icon class="mr-0">
                          <v-avatar size="64" tile>
                            <img :src="symbolImagePath(symbolIndex)" :alt="symbolImagePath(symbolIndex)">
                          </v-avatar>
                        </v-list-item-icon>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-row v-for="(payline, paylineIndex) in form.GAME_SLOTS_3D_PAYTABLE" :key="paylineIndex">
              <v-col v-for="reelIndex in [0,1,2]" :key="reelIndex" cols="12" lg="2">
                <v-select
                  v-model="payline.positions[reelIndex]"
                  :items="symbols"
                  :label="$t('Symbol')"
                >
                  <template v-slot:item="{ item }">
                    <v-avatar size="32">
                      <img v-if="item.value !== null" :src="symbolImagePath(item.value)" :alt="symbolImagePath(item.value)">
                      <span v-else>{{ $t('Any') }}</span>
                    </v-avatar>
                  </template>
                  <template v-slot:selection="{ item }">
                    <v-avatar size="32">
                      <img v-if="item.value !== null" :src="symbolImagePath(item.value)" :alt="symbolImagePath(item.value)">
                      <span v-else>{{ $t('Any') }}</span>
                    </v-avatar>
                  </template>
                </v-select>
              </v-col>
              <v-col cols="12" lg="6">
                <v-text-field
                  v-model.number="payline.payout"
                  :label="$t('Payout')"
                  :prefix="$t('Bet') + ' x '"
                  :rules="[validationPositiveInteger]"
                  outlined
                  append-outer-icon="mdi-delete"
                  @click:append-outer="deletePayline(paylineIndex)"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-btn color="primary" @click="addPayline()">
                  {{ $t('Add payline') }}
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
import FormMixin from '~/mixins/Form'
import draggable from 'vuedraggable'
import FileUpload from '~/components/Admin/FileUpload'
import ColorPicker from '~/components/ColorPicker'

export default {
  components: { ColorPicker, FileUpload, draggable },

  mixins: [FormMixin],

  props: {
    form: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      variables: {
        GAME_SLOTS_3D_CATEGORIES: config('slots-3d.categories'),
        GAME_SLOTS_3D_BANNER: config('slots-3d.banner'),
        GAME_SLOTS_3D_BACKGROUND_SKYBOX: config('slots-3d.background_skybox'),
        GAME_SLOTS_3D_BACKGROUND_SKYBOX_ROTATE: config('slots-3d.background_skybox_rotate'),
        GAME_SLOTS_3D_BACKGROUND_SKYBOX_DEEP: config('slots-3d.background_skybox_deep'),
        GAME_SLOTS_3D_TEXTURE: config('slots-3d.texture'),
        GAME_SLOTS_3D_TABLE_TEXTURE: config('slots-3d.table_texture'),
        GAME_SLOTS_3D_TABLE_COLOR: config('slots-3d.table_color'),
        GAME_SLOTS_3D_TABLE_BORDER_COLOR: config('slots-3d.table_border_color'),
        GAME_SLOTS_3D_MIN_BET: parseInt(config('slots-3d.min_bet'), 10),
        GAME_SLOTS_3D_MAX_BET: parseInt(config('slots-3d.max_bet'), 10),
        GAME_SLOTS_3D_BET_CHANGE_AMOUNT: parseInt(config('slots-3d.bet_change_amount'), 10),
        GAME_SLOTS_3D_DEFAULT_BET_AMOUNT: parseInt(config('slots-3d.default_bet_amount'), 10),
        GAME_SLOTS_3D_SYMBOLS: config('slots-3d.symbols'),
        GAME_SLOTS_3D_REELS: config('slots-3d.reels'),
        GAME_SLOTS_3D_PAYTABLE: config('slots-3d.paytable')
      }
    }
  },

  computed: {
    symbols () {
      return [{ text: this.$t('Any'), value: null }].concat(this.variables.GAME_SLOTS_3D_SYMBOLS.map((file, i) => ({ text: file, value: i })))
    }
  },

  created () {
    this.$emit('input', this.variables)
  },

  methods: {
    symbolImagePath (symbolIndex) {
      return this.form.GAME_SLOTS_3D_SYMBOLS[symbolIndex] || ''
    },
    addSymbol () {
      this.variables.GAME_SLOTS_3D_SYMBOLS.push('')
    },
    removeSymbol (symbolIndex) {
      this.variables.GAME_SLOTS_3D_SYMBOLS.splice(symbolIndex, 1)
    },
    addSymbolToReel (reelIndex, symbolIndex) {
      this.variables.GAME_SLOTS_3D_REELS[reelIndex].push(symbolIndex)
    },
    removeSymbolFromReel (reelIndex, index) {
      this.variables.GAME_SLOTS_3D_REELS[reelIndex].splice(index, 1)
    },
    addPayline () {
      this.variables.GAME_SLOTS_3D_PAYTABLE.push({ positions: [0, 0, 0], payout: 10 })
    },
    deletePayline (index) {
      this.variables.GAME_SLOTS_3D_PAYTABLE.splice(index, 1)
    }
  }
}
</script>
<style lang="scss" scoped>
  .v-badge {
    cursor: move; /* fallback if grab cursor is unsupported */
    cursor: grab;
    cursor: -moz-grab;
    cursor: -webkit-grab;
  }

  .sortable-chosen, .sortable-ghost {
    .v-badge {
      cursor: move;
    }
  }
</style>
