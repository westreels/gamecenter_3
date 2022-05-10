<template>
  <div class="d-flex flex-column fill-height py-3">
    <div class="d-flex justify-center align-center">
      <transition name="scale">
        <v-alert
          v-if="prediction && prediction.asset.id === asset.id"
          id="prediction-info-block"
          text
          color="primary"
          class="rounded"
        >
          <h3 class="mb-4 text--primary text-h5 font-weight-thin">
            {{ prediction.asset.name }}
          </h3>
          <v-row class="text--primary">
            <v-col class="text-no-wrap">
              {{ $t('Open price') }}:
              <b>{{ chart.numberFormatter.format(prediction.open_price) }}</b>
            </v-col>
            <v-col class="text-no-wrap">
              {{ $t('Direction') }}:
              <b>{{ prediction.direction > 0 ? $t('Higher') : $t('Lower') }}</b>
            </v-col>
          </v-row>
          <v-row v-if="prediction.close_price" class="text--primary">
            <v-col class="text-no-wrap">
              {{ $t('Close price') }}:
              <b>{{ chart.numberFormatter.format(prediction.close_price) }}</b>
            </v-col>
            <v-col class="text-no-wrap">
              {{ $t('Win') }}:
              <b>{{ win }}</b>
            </v-col>
          </v-row>
          <v-row v-else class="text--primary">
            <v-col class="text-center">
              <countdown-timer :end-date="predictionTimerEndDate" />
            </v-col>
          </v-row>
        </v-alert>
      </transition>
    </div>
    <div class="d-flex justify-center fill-height align-center">
      <div v-show="assetDataIsLoaded" ref="chart" class="chart"></div>
      <block-preloader v-show="!assetDataIsLoaded" />
    </div>
    <div class="d-flex justify-center flex-column">
      <prediction-controls
        :loading="requestInProgress"
        :input-disabled="gameInProgress || !assetDataIsLoaded"
        :search-disabled="requestInProgress || !assetDataIsLoaded"
        @play="play"
        @asset="init"
      />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { route } from '~/plugins/route'
import * as am4core from '@amcharts/amcharts4/core'
import * as am4charts from '@amcharts/amcharts4/charts'
import am4themes_animated from '@amcharts/amcharts4/themes/animated'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import PredictionMixin from '~/mixins/Prediction'
import SoundMixin from '~/mixins/Sound'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/crypto-prediction/resources/audio/win.wav'
import loseSound from 'packages/crypto-prediction/resources/audio/lose.wav'
import PredictionControls from '~/components/Predictions/PredictionControls'
import colors from 'vuetify/lib/util/colors'
import CountdownTimer from '~/components/CountdownTimer'
import BlockPreloader from '~/components/BlockPreloader'
import { config } from '~/plugins/config'

am4core.useTheme(am4themes_animated)

export default {
  name: 'CryptoPrediction',

  components: {
    BlockPreloader,
    CountdownTimer,
    PredictionControls
  },

  mixins: [FormMixin, PredictionMixin, SoundMixin],

  data () {
    return {
      clickSound,
      formIsValid: true,
      requestInProgress: false,
      gameInProgress: false,
      asset: null,
      chart: null,
      prediction: null,
      assetDataIsLoaded: false,
      predictionTimerEndDate: null,
      predictionInfoBlockTimeoutId: null,
      assetPriceUpdateIntervalId: null,
      win: 0
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    assetPriceUpdateInterval () {
      return Math.max(1, parseInt(config('crypto-prediction.price_update_interval'), 10)) * 1000
    }
  },

  beforeDestroy () {
    this.clearPredictionInfoBlockTimeout()
    this.clearAssetPriceUpdateInterval()
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance'
    }),
    async init (asset) {
      this.assetDataIsLoaded = false

      this.gameInProgress = false
      this.asset = { ...asset }
      this.prediction = null
      this.clearPredictionInfoBlockTimeout()
      this.clearAssetPriceUpdateInterval()

      const [game, history] = await Promise.all([this.fetchGame(asset), this.fetchAssetHistory(asset)])

      this.assetDataIsLoaded = true

      if (game) {
        this.gameInProgress = true
        this.prediction = { ...game.gameable }
        this.predictionTimerEndDate = this.prediction.end_time_unix
        this.setGameCompletionTimeout(game)
      }

      this.makeChart(history)

      this.assetPriceUpdateIntervalId = setInterval(() => {
        this.updateAssetPrice(asset)
      }, this.assetPriceUpdateInterval)

      this.updateAssetPrice(asset)
    },
    async fetchGame (asset) {
      const { data } = await axios.get(this.getRoute('index').replace('{asset}', asset.id))
      return data
    },
    async fetchAssetHistory (asset) {
      const { data } = await axios.get(route('assets.history').replace('{asset}', asset.id))
      return data
    },
    async updateAssetPrice (asset) {
      const { data: price } = await axios.get(route('assets.price').replace('{asset}', asset.id))
      this.pushLastPriceToChart(price)
    },
    makeChart (data) {
      if (this.chart) {
        this.chart.dispose()
      }

      const chart = am4core.create(this.$refs.chart, am4charts.XYChart)
      chart.hiddenState.properties.opacity = 0

      chart.padding(0, 0, 0, 0)
      chart.zoomOutButton.disabled = true
      // chart.preloader.disabled = true
      chart.data = data

      // let indicator;
      // function showIndicator() {
      //   indicator = chart.tooltipContainer.createChild(am4core.Container);
      //   indicator.background.fill = am4core.color("#fff");
      //   indicator.background.fillOpacity = 0.8;
      //   indicator.width = am4core.percent(100);
      //   indicator.height = am4core.percent(100);
      //
      //   let indicatorLabel = indicator.createChild(am4core.Label);
      //   indicatorLabel.text = "Loading stuff...";
      //   indicatorLabel.align = "center";
      //   indicatorLabel.valign = "middle";
      //   indicatorLabel.fontSize = 20;
      // }
      //
      // showIndicator();

      const dateAxis = chart.xAxes.push(new am4charts.DateAxis())
      dateAxis.renderer.grid.template.location = 0
      dateAxis.renderer.minGridDistance = 30
      dateAxis.baseInterval = { timeUnit: 'second', count: 1 }
      dateAxis.dateFormats.setKey('second', 'hh:mm:ss')
      dateAxis.periodChangeDateFormats.setKey('second', 'hh:mm:ss')
      dateAxis.periodChangeDateFormats.setKey('minute', 'hh:mm:ss')
      dateAxis.periodChangeDateFormats.setKey('hour', 'hh:mm:ss')
      dateAxis.renderer.inside = true
      dateAxis.renderer.axisFills.template.disabled = true
      dateAxis.renderer.ticks.template.disabled = true
      dateAxis.renderer.grid.template.disabled = true
      dateAxis.skipEmptyPeriods = true
      dateAxis.visible = false
      dateAxis.rangeChangeDuration = 500
      dateAxis.groupData = true
      dateAxis.groupCount = 100
      dateAxis.groupInterval = { timeUnit: 'minute', count: 1 }

      const valueAxis = chart.yAxes.push(new am4charts.ValueAxis())
      // valueAxis.tooltip.disabled = true
      valueAxis.interpolationDuration = 500
      valueAxis.rangeChangeDuration = 500
      valueAxis.renderer.inside = true
      valueAxis.renderer.minLabelPosition = 0.05
      valueAxis.renderer.maxLabelPosition = 0.95
      valueAxis.renderer.axisFills.template.disabled = true
      valueAxis.renderer.ticks.template.disabled = true
      valueAxis.renderer.grid.template.disabled = true
      valueAxis.renderer.labels.template.fill = am4core.color(this.$vuetify.theme.isDark ? '#fff' : '#000')

      const series = chart.series.push(new am4charts.LineSeries())
      series.dataFields.dateX = 'date'
      series.dataFields.valueY = 'value'
      series.groupFields.valueY = 'close'
      series.defaultState.transitionDuration = 500
      series.tensionX = 0.8
      series.stroke = am4core.color(this.$vuetify.theme.currentTheme.primary)
      series.strokeWidth = 2
      series.tooltipText = '{valueY}'
      series.showTooltipOn = 'always'
      series.tooltip.getFillFromObject = false
      series.tooltip.pointerOrientation = 'vertical'
      series.tooltip.label.fill = am4core.color('#fff')
      series.tooltip.dx = 0
      series.tooltip.dy = -5
      series.fillOpacity = 1 // gradient fill of the series

      const gradient = new am4core.LinearGradient()
      gradient.addColor(this.$vuetify.theme.currentTheme.primary, 0.25)
      gradient.addColor(this.$vuetify.theme.currentTheme.primary, 0)
      series.fill = gradient

      chart.events.on('datavalidated', () => {
        dateAxis.zoom({
          start: 0,
          end: 1.2
        }, false, true)
      })

      const lastPriceBullet = series.bullets.push(new am4charts.CircleBullet())
      lastPriceBullet.circle.fill = am4core.color(this.$vuetify.theme.currentTheme.primary)
      lastPriceBullet.tooltipText = '{valueY}'
      lastPriceBullet.disabled = true
      lastPriceBullet.propertyFields.disabled = 'isLastPriceTooltipDisabled'
      lastPriceBullet.showTooltipOn = 'always'

      // const buyBullet = series.bullets.push(new am4core.Sprite())
      // buyBullet.path = 'M15,20H9V12H4.16L12,4.16L19.84,12H15V20Z'
      // buyBullet.width = 24
      // buyBullet.height = 24
      // buyBullet.fill = am4core.color(colors.green.base)
      // buyBullet.horizontalCenter = 'middle'
      // buyBullet.verticalCenter = 'middle'
      // buyBullet.disabled = true
      // buyBullet.propertyFields.disabled = 'isBuyBulletDisabled'

      // const sellBullet = series.bullets.push(new am4core.Sprite())
      // sellBullet.path = 'M9,4H15V12H19.84L12,19.84L4.16,12H9V4Z'
      // sellBullet.width = 24
      // sellBullet.height = 24
      // sellBullet.fill = am4core.color(colors.red.base)
      // sellBullet.horizontalCenter = 'middle'
      // sellBullet.verticalCenter = 'middle'
      // sellBullet.disabled = true
      // sellBullet.propertyFields.disabled = 'isSellBulletDisabled'

      this.chart = chart
    },
    pushLastPriceToChart (value) {
      const chart = this.chart
      const lastDataItem = chart.data[chart.data.length - 1]

      if (value > 0) {
        const date = new Date().getTime()
        const valueDifference = value - lastDataItem.value

        // disable tooltip for current last price
        lastDataItem.isLastPriceTooltipDisabled = true

        // add new data item with tooltip enabled
        chart.addData({ date, value, isLastPriceTooltipDisabled: false }, 0)

        chart.series.values[0].tooltip.background.fill = valueDifference > 0
          ? am4core.color(colors.green.base)
          : (valueDifference < 0
              ? am4core.color(colors.red.base)
              : am4core.color(colors.grey.base))
      }
    },
    async play ({ asset, bet, direction, duration }) {
      this.requestInProgress = true
      this.gameInProgress = true
      this.prediction = null
      this.clearPredictionInfoBlockTimeout()

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      // execute the action
      const { data: game } = await axios.post(
        this.getRoute('make').replace('{asset}', asset.id),
        { bet, direction, duration }
      )

      this.requestInProgress = false
      this.prediction = { ...game.gameable }
      this.predictionTimerEndDate = game.server_time / 1000 + duration
      this.pushLastPriceToChart(game.gameable.open_price)
      this.setGameCompletionTimeout(game)
    },
    async setGameCompletionTimeout (game) {
      setTimeout(async () => {
        const { data: completedGame } = await axios.post(this.getRoute('complete').replace('{game}', game.id))

        this.updateUserAccountBalance(completedGame.account.balance + completedGame.win)

        if (completedGame.gameable.asset.id === this.asset.id) {
          this.prediction = { ...completedGame.gameable }
          this.win = completedGame.win

          if (completedGame.win > 0) {
            this.sound(winSound)
          } else {
            this.sound(loseSound)
          }

          this.gameInProgress = false

          this.predictionInfoBlockTimeoutId = setTimeout(() => {
            this.prediction = null
          }, 10000)
        }
      }, ((game.gameable.end_time_unix + 1) * 1000 - game.server_time))
    },
    clearPredictionInfoBlockTimeout () {
      if (this.predictionInfoBlockTimeoutId) {
        clearTimeout(this.predictionInfoBlockTimeoutId)
        this.predictionInfoBlockTimeoutId = null
      }
    },
    clearAssetPriceUpdateInterval () {
      if (this.assetPriceUpdateIntervalId) {
        clearInterval(this.assetPriceUpdateIntervalId)
        this.assetPriceUpdateIntervalId = null
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.chart {
  width: 100%;
  height: 100%
}

#prediction-info-block {
  z-index: 1;
  position:absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);

  &::v-deep {
    &:before {
      opacity: 0.4 !important;
    }
  }
}
</style>
