<template>
  <v-app :class="navbarVisible ? 'permanent-navbar' : 'temporary-navbar'">
    <system-bar v-if="systemBarEnabled && authenticated" />

    <v-navigation-drawer v-model="navigationDrawer" app :permanent="navbarVisible" :temporary="!navbarVisible">
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">
            {{ $t('Navigation') }}
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-divider />
      <template v-if="user && user.is_admin">
        <main-menu />
        <v-divider />
      </template>
      <v-list dense>
        <v-list-item :to="{ name: 'home' }" link exact>
          <v-list-item-action>
            <v-icon>mdi-home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ $t('Home') }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <template v-if="authenticated">
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.deposits.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-plus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Deposits') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.withdrawals.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-minus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Withdrawals') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-group
            prepend-icon="mdi-cards-playing-outline"
            no-action
          >
            <template v-slot:activator>
              <v-list-item-title>{{ $t('Games') }}</v-list-item-title>
            </template>
            <template v-for="(game, i) in games">
              <v-list-item :key="i" :to="{ name: 'game', params: { game: game.id, slug: game.slug } }" link exact>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ game.name }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list-group>
          <v-list-group
            v-if="Object.keys(predictions).length"
            prepend-icon="mdi-trending-up"
            no-action
          >
            <template v-slot:activator>
              <v-list-item-title>{{ $t('Markets') }}</v-list-item-title>
            </template>
            <template v-for="(pkg, id) in predictions">
              <v-list-item :key="id" :to="{ name: 'prediction', params: { packageId: id }}" link exact>
                <v-list-item-content>
                  <v-list-item-title>{{ pkg.name }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list-group>
          <v-list-item v-if="rafflePackageEnabled" :to="{ name: 'raffles' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-ticket</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Raffles') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'history' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-history</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('History') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="leaderboardPageEnabled" :to="{ name: 'leaderboard' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-star</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Leaderboard') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>

    <chat v-if="authenticated && chatEnabled" v-model="chatDrawer" @message="setUnreadChatMessagesCount" />

    <v-app-bar app :clipped-left="!navbarVisible">
      <v-app-bar-nav-icon v-if="!navbarVisible" @click.stop="navigationDrawer = !navigationDrawer" />

      <v-toolbar-title class="d-flex align-center">
        <router-link :to="{ name: 'home' }">
          <v-avatar size="40" tile>
            <v-img :src="appLogoUrl" :alt="appName" />
          </v-avatar>
        </router-link>
        <div class="ml-3 d-none d-sm-block">
          {{ appName }}
        </div>
      </v-toolbar-title>

      <v-spacer />

      <template v-if="!token && !authenticated">
<!--        <v-btn :to="{ name: 'login' }" class="secondary">{{ $t('Log in') }}</v-btn>-->
<!--        <v-btn :to="{ name: 'register' }" class="primary ml-2">{{ $t('Sign up') }}</v-btn>-->
        <a style="text-decoration: none;" :href="urlLogin">
          <v-btn class="secondary">
            Login
          </v-btn>
        </a>
        <a style="text-decoration: none;" :href="urlSignUp">
          <v-btn class="primary ml-2">
            Sign up
          </v-btn>
        </a>
      </template>
      <template v-else-if="authenticated">
        <v-menu v-model="accountMenu" offset-y left>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              rounded
              class="px-2 width-auto"
              v-on="on"
            >
              <v-icon class="mr-1">mdi-poker-chip</v-icon>
              <animated-number v-if="account" :number="account.balance" />
            </v-btn>
          </template>
          <v-list>
            <v-subheader class="text-uppercase">{{ $t('Account') }}</v-subheader>
            <v-list-item :to="{ name: 'user.account.transactions' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-format-list-bulleted</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Transactions') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.deposits.index' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-cash-plus</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Deposits') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.withdrawals.index' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-cash-minus</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Withdrawals') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="faucetEnabled" :to="{ name: 'user.account.faucet' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-chip</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Faucet') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-menu v-model="settingsMenu" :close-on-content-click="false" offset-y left>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-icon>mdi-cog</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-list>
              <v-subheader class="text-uppercase">
                {{ $t('Settings') }}
              </v-subheader>
              <v-list-item>
                <v-list-item-action>
                  <v-switch
                    :input-value="settings.sounds"
                    :value="settings.sounds"
                    color="primary"
                    @change="saveSettings('sounds', $event)"
                  />
                </v-list-item-action>
                <v-list-item-title>{{ $t('Sounds') }}</v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-action>
                  <v-switch
                    :input-value="isMobile ? false : settings.gameFeed"
                    :value="settings.gameFeed"
                    :disabled="isMobile"
                    color="primary"
                    @change="saveSettings('gameFeed', $event)"
                  />
                </v-list-item-action>
                <v-list-item-title>{{ $t('Game feed') }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>

        <v-menu v-model="userMenu" :close-on-content-click="false" offset-y>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-avatar size="40">
                <img :src="user.avatar" :alt="user.name">
              </v-avatar>
            </v-btn>
          </template>

          <v-card>
            <v-list>
              <v-list-item>
                <v-list-item-avatar>
                  <img :src="user.avatar" :alt="user.name">
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title>{{ user.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action class="flex-row">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'users.show', params: { id: user.id } }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-account</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Profile') }}</span>
                  </v-tooltip>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'user.security' }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-shield-lock</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Security') }}</span>
                  </v-tooltip>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'user.affiliate' }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-link</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Affiliate program') }}</span>
                  </v-tooltip>
                </v-list-item-action>
              </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-card-actions>
              <v-spacer />
              <v-btn text @click="logout">
                <v-icon>mdi-logout</v-icon>
                {{ $t('Log out') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>

        <v-btn v-if="chatEnabled" icon @click="chatDrawer = !chatDrawer">
          <v-badge :content="unreadChatMessagesCount" :value="unreadChatMessagesCount" overlap>
            <v-icon>{{ chatDrawer ? 'mdi-message' : 'mdi-message-outline' }}</v-icon>
          </v-badge>
        </v-btn>
      </template>
      <preloader :active="loading" />
    </v-app-bar>


    <v-main>
      <message />
      <router-view id="content" />
      <div style="position: fixed; top: 20px; right: 20px">
        <v-icon class="mr-1">mdi-poker-chip</v-icon>
        <animated-number v-if="account" :number="account.balance" />
      </div>

    </v-main>

    <component :is="footerComponent" :inset="navbarVisible" />
  </v-app>
</template>

<script>
import { config } from '~/plugins/config'
import { mapState, mapGetters } from 'vuex'
import DeviceMixin from '~/mixins/Device'
import Message from '~/components/Message'
import Chat from '~/components/Chat'
import Preloader from '~/components/Preloader'
import PrimaryFooter from '~/components/PrimaryFooter'
import SecondaryFooter from '~/components/SecondaryFooter'
import AdminFooter from '~/components/Admin/Footer'
import AnimatedNumber from '~/components/AnimatedNumber'
import SystemBar from '~/components/SystemBar'
import MainMenu from '~/components/Admin/MainMenu'

export default {
  name: 'DefaultLayout',

  components: { MainMenu, SystemBar, Message, Chat, Preloader, PrimaryFooter, SecondaryFooter, AdminFooter, AnimatedNumber },

  mixins: [DeviceMixin],

  data () {
    return {
      navigationDrawer: this.navbarVisible,
      chatDrawer: false,
      accountMenu: false,
      settingsMenu: false,
      userMenu: false,
      unreadChatMessagesCount: 0
    }
  },

  computed: {
    ...mapState('auth', ['user', 'account', 'token']),
    ...mapState('settings', ['settings']),
    ...mapState('progress', ['loading']),
    ...mapGetters({
      authenticated: 'auth/check',
      games: 'package-manager/getGames'
    }),
    appName () {
      return config('app.name')
    },
    urlLogin () {
      const domain = window.config.define.social.domain;
      const appId = window.config.define.social.app_id;
      const appSecret = window.config.define.social.app_secret;

      return `${domain}/oauth?app_id=${appId}&app_secret=${appSecret}`
    },
    urlSignUp () {
      const domain = window.config.define.social.domain;
      return `${domain}/register`
    },
    appLogoUrl () {
      return config('app.logo')
    },
    navbarVisible () {
      return config('settings.interface.navbar.visible') && !this.isMobile
    },
    systemBarEnabled () {
      return config('settings.interface.system_bar.enabled')
    },
    chatEnabled () {
      return config('settings.interface.chat.enabled')
    },
    faucetEnabled () {
      return config('settings.bonuses.faucet.amount') > 0
    },
    leaderboardPageEnabled () {
      return config('settings.content.leaderboard.enabled')
    },
    rafflePackageEnabled () {
      return this.$store.getters['package-manager/getById']('raffle')
    },
    paymentsPackageEnabled () {
      return this.$store.getters['package-manager/getById']('payments')
    },
    predictions () {
      return this.$store.getters['package-manager/getByType']('prediction')
    },
    footerComponent () {
      if (!this.$route.name) {
        return false
      }

      return this.$route.name.indexOf('admin.') > -1
        ? 'AdminFooter'
        : (!this.navbarVisible && ['game', 'prediction'].indexOf(this.$route.name) === -1 && this.$route.name.indexOf('admin.') === -1
            ? 'PrimaryFooter'
            : 'SecondaryFooter')
    }
  },

  methods: {
    config,
    setUnreadChatMessagesCount (count) {
      this.unreadChatMessagesCount = count
    },
    async logout () {
      // Log out
      await this.$store.dispatch('auth/logout')

      // Redirect to home page
      if (this.$route.name !== 'home') {
        this.$router.push({ name: 'home' })
      }
    },
    saveSettings (key, value) {
      // value is null when switch is turned off
      this.$store.dispatch('settings/set', { key, value: !!value })
    }
  }
}
</script>
