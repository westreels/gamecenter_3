<template>
  <v-container>
    <v-card>
      <v-card-title>
        {{ $t('Settings') }}
      </v-card-title>
      <v-card-text>
        <v-form v-model="formIsValid" @submit.prevent="save">
          <v-tabs vertical>
            <v-tab>
              {{ $t('General') }}
            </v-tab>
            <v-tab>
              {{ $t('Theme') }}
            </v-tab>
            <v-tab>
              {{ $t('Interface') }}
            </v-tab>
            <v-tab>
              {{ $t('Content') }}
            </v-tab>
            <v-tab>
              {{ $t('Security') }}
            </v-tab>
            <v-tab>
              {{ $t('Games') }}
            </v-tab>
            <v-tab>
              {{ $t('Bots') }}
            </v-tab>
            <v-tab>
              {{ $t('Bonuses') }}
            </v-tab>
            <v-tab>
              {{ $t('Affiliate program') }}
            </v-tab>
            <v-tab>
              {{ $t('Services') }}
            </v-tab>
            <v-tab>
              {{ $t('OAuth providers') }}
            </v-tab>
            <v-tab class="text-left">
              {{ $t('Formatting') }}
            </v-tab>
            <v-tab class="text-left">
              {{ $t('E-mail') }}
            </v-tab>
            <v-tab>
              {{ $t('Logging') }}
            </v-tab>
            <template v-for="(pkg, id) in packages">
              <v-tab v-if="packageComponents[id]" :key="id">
                {{ $t('Add-on') }}: {{ pkg.name }}
              </v-tab>
            </template>

            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-text-field
                    v-model="form.APP_NAME"
                    :label="$t('App name')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('APP_NAME')"
                    :error-messages="form.errors.get('APP_NAME')"
                    outlined
                    @keydown="clearFormErrors($event,'APP_NAME')"
                  />

                  <v-select
                    v-model="form.LOCALE"
                    :items="languages"
                    :label="$t('Default language')"
                    :error="form.errors.has('LOCALE')"
                    :error-messages="form.errors.get('LOCALE')"
                    outlined
                  />

                  <file-upload
                    v-model="form.APP_LOGO"
                    :label="$t('Logo')"
                    name="logo"
                    folder="images"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                    v-model="form.THEME_MODE"
                    :items="modes"
                    :label="$t('Mode')"
                    :error="form.errors.has('THEME_MODE')"
                    :error-messages="form.errors.get('THEME_MODE')"
                    outlined
                  />

                  <template v-for="color in colors">
                    <color-picker :key="color" v-model="form[color]" :label="colorText(color)" />
                  </template>

                  <v-select
                    v-model="form.THEME_BACKGROUND"
                    :items="backgrounds"
                    :label="$t('Background')"
                    :error="form.errors.has('THEME_BACKGROUND')"
                    :error-messages="form.errors.get('THEME_BACKGROUND')"
                    outlined
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model.number="form.INTERFACE_CREDITS_ICON"
                          :label="$t('Balance icon')"
                          :prepend-inner-icon="form.INTERFACE_CREDITS_ICON"
                          :rules="[validationRequired]"
                          :error="form.errors.has('INTERFACE_CREDITS_ICON')"
                          :error-messages="form.errors.get('INTERFACE_CREDITS_ICON')"
                          :hint="$t('Choose icons at https://materialdesignicons.com')"
                          :persistent-hint="true"
                          outlined
                          @keydown="clearFormErrors($event,'INTERFACE_CREDITS_ICON')"
                        />

                        <v-switch
                          v-model="form.INTERFACE_NAVBAR_VISIBLE"
                          :label="$t('Navigation bar always visible')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                          :hint="$t('Except for mobiles')"
                          :persistent-hint="true"
                          class="mb-5"
                        />

                        <v-switch
                          v-model="form.INTERFACE_SYSTEM_BAR_ENABLED"
                          :label="$t('Display system bar')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                          :disabled="!pusherEnabled"
                          :hint="$t('Please fill Pusher settings first under Services tab.')"
                          :persistent-hint="!pusherEnabled"
                          class="mb-5"
                        />

                        <v-switch
                          v-model="form.INTERFACE_ONLINE_ENABLED"
                          :label="$t('Display users online status')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                          :disabled="!pusherEnabled"
                          :hint="$t('Please fill Pusher settings first under Services tab.')"
                          :persistent-hint="!pusherEnabled"
                          class="mb-5"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Chat') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-switch
                          v-model="form.CHAT_ENABLED"
                          :label="$t('Enable')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                          :disabled="!pusherEnabled"
                          :hint="$t('Please fill Pusher settings first under Services tab.')"
                          :persistent-hint="!pusherEnabled"
                          class="mb-5"
                        />

                        <v-text-field
                          v-if="form.CHAT_ENABLED === 'true'"
                          v-model.number="form.CHAT_MESSAGE_MAX_LENGTH"
                          :label="$t('Chat message max length')"
                          :rules="[validationInteger, validationPositiveNumber]"
                          :error="form.errors.has('CHAT_MESSAGE_MAX_LENGTH')"
                          :error-messages="form.errors.get('CHAT_MESSAGE_MAX_LENGTH')"
                          outlined
                          @keydown="clearFormErrors($event,'CHAT_MESSAGE_MAX_LENGTH')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Home page') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-expansion-panels>
                          <v-expansion-panel>
                            <v-expansion-panel-header>{{ $t('Content') }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <file-editor name="home" />
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                          <v-expansion-panel>
                            <v-expansion-panel-header>{{ $t('Slider') }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-text-field
                                v-model.number="form.CONTENT_HOME_SLIDER.height"
                                :label="$t('Height')"
                                :rules="[validationInteger, validationPositiveNumber]"
                                :suffix="$t('px')"
                                outlined
                                class="mt-3"
                              />

                              <v-switch
                                v-model="form.CONTENT_HOME_SLIDER.navigation"
                                :label="$t('Navigation')"
                                color="primary"
                                :false-value="false"
                                :true-value="true"
                              />

                              <v-switch
                                v-model="form.CONTENT_HOME_SLIDER.pagination"
                                :label="$t('Pagination')"
                                color="primary"
                                :false-value="false"
                                :true-value="true"
                              />

                              <v-switch
                                v-model="form.CONTENT_HOME_SLIDER.cycle"
                                :label="$t('Auto rotate slides')"
                                color="primary"
                                :false-value="false"
                                :true-value="true"
                              />

                              <v-text-field
                                v-show="form.CONTENT_HOME_SLIDER.cycle"
                                v-model.number="form.CONTENT_HOME_SLIDER.interval"
                                :label="$t('Interval')"
                                :rules="[validationInteger, validationPositiveNumber]"
                                :suffix="$t('seconds')"
                                outlined
                                class="mt-3"
                              />

                              <v-expansion-panels>
                                <v-expansion-panel
                                  v-for="(slide, i) in form.CONTENT_HOME_SLIDER.slides"
                                  :key="i"
                                >
                                  <v-expansion-panel-header>
                                    {{ $t('Slide {0}', [i+1]) }}
                                  </v-expansion-panel-header>
                                  <v-expansion-panel-content>
                                    <v-text-field
                                      v-model="form.CONTENT_HOME_SLIDER.slides[i].title"
                                      :label="$t('Title')"
                                      outlined
                                    />

                                    <v-text-field
                                      v-model="form.CONTENT_HOME_SLIDER.slides[i].subtitle"
                                      :label="$t('Subtitle')"
                                      outlined
                                    />

                                    <file-upload
                                      v-model="form.CONTENT_HOME_SLIDER.slides[i].image.url"
                                      :label="$t('Image')"
                                      :name="`home-slide-${i}`"
                                      folder="images"
                                    />

                                    <v-text-field
                                      v-model="form.CONTENT_HOME_SLIDER.slides[i].link.title"
                                      :label="$t('Action button title')"
                                      outlined
                                    />

                                    <v-text-field
                                      v-model="form.CONTENT_HOME_SLIDER.slides[i].link.url"
                                      :label="$t('Action button URL')"
                                      outlined
                                    />

                                    <v-btn v-if="form.CONTENT_HOME_SLIDER.slides.length > 1" color="primary" @click="deleteSlide(i)">
                                      {{ $t('Delete slide') }}
                                    </v-btn>
                                  </v-expansion-panel-content>
                                </v-expansion-panel>
                              </v-expansion-panels>

                              <v-btn color="primary" class="mt-5" @click="addSlide">
                                {{ $t('Add slide') }}
                              </v-btn>

                            </v-expansion-panel-content>
                          </v-expansion-panel>
                          <v-expansion-panel>
                            <v-expansion-panel-header>{{ $t('Original games list') }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-select
                                v-model="form.CONTENT_HOME_GAMES_DISPLAY_STYLE"
                                :items="gameDisplayStyles"
                                :label="$t('Display style')"
                                outlined
                              />

                              <v-text-field
                                v-model.number="form.CONTENT_HOME_GAMES_DISPLAY_COUNT"
                                :label="$t('Display count')"
                                :rules="[validationPositiveInteger]"
                                :hint="$t('How many games are preloaded.')"
                                :persistent-hint="true"
                                outlined
                              />

                              <v-text-field
                                v-model.number="form.CONTENT_HOME_GAMES_LOAD_COUNT"
                                :label="$t('Load count')"
                                :rules="[validationPositiveInteger]"
                                :hint="$t('How many games are loaded when \'Load more\' button is clicked.')"
                                :persistent-hint="true"
                                class="mt-5"
                                outlined
                              />
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                          <v-expansion-panel v-if="gameProvidersPackageEnabled">
                            <v-expansion-panel-header>{{ $t('Provider games list') }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-expansion-panels>
                                <v-expansion-panel>
                                  <v-expansion-panel-header>{{ $t('Featured categories') }}</v-expansion-panel-header>
                                  <v-expansion-panel-content>
                                    <v-row v-for="(category, k) in form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES" :key="k">
                                      <v-col cols="12" md="6">
                                        <v-text-field
                                          v-model="form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES[k].title"
                                          :label="$t('Title')"
                                          :rules="[validationRequired]"
                                          outlined
                                          hide-details
                                        />
                                      </v-col>
                                      <v-col cols="12" md="6">
                                        <v-combobox
                                          v-model="form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES[k].categories"
                                          :label="$t('Categories')"
                                          multiple
                                          outlined
                                          chips
                                          small-chips
                                          deletable-chips
                                          hide-selected
                                          no-filter
                                          hide-details
                                          :append-outer-icon="form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES.length > 1 ? 'mdi-delete' : ''"
                                          @click:append-outer="deleteFeaturedCategory(k)"
                                        />
                                      </v-col>
                                    </v-row>
                                    <v-row>
                                      <v-col>
                                        <v-btn color="primary" @click="addFeaturedCategory">
                                          {{ $t('Add category') }}
                                        </v-btn>
                                      </v-col>
                                    </v-row>
                                  </v-expansion-panel-content>
                                </v-expansion-panel>
                              </v-expansion-panels>

                              <v-select
                                v-model="form.CONTENT_HOME_PROVIDER_GAMES_DISPLAY_STYLE"
                                :items="gameDisplayStyles"
                                :label="$t('Display style')"
                                class="mt-5"
                                outlined
                              />

                              <v-text-field
                                v-model.number="form.CONTENT_HOME_PROVIDER_GAMES_DISPLAY_COUNT"
                                :label="$t('Display count')"
                                :rules="[validationPositiveInteger]"
                                :hint="$t('How many games are preloaded.')"
                                :persistent-hint="true"
                                outlined
                              />

                              <v-text-field
                                v-model.number="form.CONTENT_HOME_PROVIDER_GAMES_LOAD_COUNT"
                                :label="$t('Load count')"
                                :rules="[validationPositiveInteger]"
                                :hint="$t('How many games are loaded when \'Load more\' button is clicked.')"
                                :persistent-hint="true"
                                class="mt-5"
                                outlined
                              />
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Social') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-expansion-panels>
                          <v-expansion-panel
                            v-for="(link, i) in form.CONTENT_SOCIAL"
                            :key="i"
                          >
                            <v-expansion-panel-header>
                              {{ form.CONTENT_SOCIAL[i].title }}
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-text-field
                                v-model="form.CONTENT_SOCIAL[i].title"
                                :label="$t('Title')"
                                :rules="[validationRequired]"
                                outlined
                              />

                              <v-text-field
                                v-model="form.CONTENT_SOCIAL[i].icon"
                                :label="$t('Icon')"
                                :rules="[validationRequired]"
                                :prepend-inner-icon="link.icon"
                                outlined
                                :hint="$t('Choose icons at https://materialdesignicons.com')"
                                :persistent-hint="true"
                                class="mb-5"
                              />

                              <color-picker v-model="form.CONTENT_SOCIAL[i].color" :label="$t('Icon color')" />

                              <v-text-field
                                v-model="form.CONTENT_SOCIAL[i].url"
                                :label="$t('URL')"
                                :rules="[validationRequired]"
                                outlined
                                append-icon="mdi-open-in-new"
                                @click:append="openLink(form.CONTENT_SOCIAL[i].url)"
                              />

                              <v-btn color="error" @click="form.CONTENT_SOCIAL.splice(i, 1)" class="mt-5">
                                {{ $t('Delete social network') }}
                              </v-btn>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <v-btn color="primary" class="mt-5" @click="form.CONTENT_SOCIAL.push({ title: 'Network', icon: 'mdi-email', color: 'primary', url: 'https://network.com'  })">
                          {{ $t('Add social network') }}
                        </v-btn>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Leaderboard') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-switch
                          v-model="form.CONTENT_LEADERBOARD_ENABLED"
                          :label="$t('Enabled')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Footer menu') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-expansion-panels>
                          <v-expansion-panel
                            v-for="(page, i) in form.CONTENT_FOOTER_MENU"
                            :key="i"
                          >
                            <v-expansion-panel-header>
                              {{ form.CONTENT_FOOTER_MENU[i].title }}
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-text-field
                                v-model="form.CONTENT_FOOTER_MENU[i].title"
                                :label="$t('Page title')"
                                :rules="[validationRequired]"
                                outlined
                              />

                              <v-text-field
                                v-model="form.CONTENT_FOOTER_MENU[i].id"
                                :label="$t('Page slug')"
                                :rules="[validationRequired]"
                                outlined
                                :prefix="config('app.url') + '/pages/'"
                                append-icon="mdi-open-in-new"
                                @click:append="openLink(`/pages/${form.CONTENT_FOOTER_MENU[i].id}`)"
                              />

                              <file-editor :name="form.CONTENT_FOOTER_MENU[i].id" :button-label="$t('Save page content')" />

                              <v-btn color="error" @click="form.CONTENT_FOOTER_MENU.splice(i, 1)" class="mt-5">
                                {{ $t('Delete menu item') }}
                              </v-btn>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <v-btn color="primary" class="mt-5" @click="form.CONTENT_FOOTER_MENU.push({ id: 'new-page', title: 'New page' })">
                          {{ $t('Add menu item') }}
                        </v-btn>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-text-field
                    v-model.number="form.SESSION_LIFETIME"
                    :label="$t('Session lifetime')"
                    :rules="[validationInteger, validationPositiveNumber]"
                    :error="form.errors.has('SESSION_LIFETIME')"
                    :error-messages="form.errors.get('SESSION_LIFETIME')"
                    :suffix="$t('minutes')"
                    outlined
                  />

                  <v-switch
                    v-model="form.EMAIL_VERIFICATION"
                    :label="$t('Require email verification')"
                    color="primary"
                    false-value="false"
                    true-value="true"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                      v-model="form.GAMES_PLAYING_CARDS_DECK"
                      :items="decks"
                      :label="$t('Card deck')"
                      :error="form.errors.has('GAMES_PLAYING_CARDS_DECK')"
                      :error-messages="form.errors.get('GAMES_PLAYING_CARDS_DECK')"
                      :hint="form.GAMES_PLAYING_CARDS_DECK ? $t('Card deck images are located in {0}', [`public/images/games/playing-cards/${form.GAMES_PLAYING_CARDS_DECK}`]) : ''"
                      persistent-hint
                      outlined
                  />

                  <file-upload
                    v-if="!form.GAMES_PLAYING_CARDS_DECK"
                    v-model="form.GAMES_PLAYING_CARDS_FRONT_IMAGE"
                    :label="$t('Playing card front background image')"
                    name="card-front"
                    folder="images"
                  />

                  <file-upload
                    v-if="!form.GAMES_PLAYING_CARDS_DECK"
                    v-model="form.GAMES_PLAYING_CARDS_BACK_IMAGE"
                    :label="$t('Playing card back background image')"
                    name="card-back"
                    folder="images"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Games') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.BOTS_GAMES_FREQUENCY"
                          :items="frequencies"
                          :label="$t('Frequency')"
                          :error="form.errors.has('BOTS_GAMES_FREQUENCY')"
                          :error-messages="form.errors.get('BOTS_GAMES_FREQUENCY')"
                          :hint="$t('How often bots will awake.')"
                          persistent-hint
                          outlined
                        />

                        <v-text-field
                          v-if="form.BOTS_GAMES_FREQUENCY"
                          v-model.number="form.BOTS_GAMES_MIN_COUNT"
                          :label="$t('Min bots')"
                          :rules="[validationInteger, validationPositiveNumber]"
                          :error="form.errors.has('BOTS_GAMES_MIN_COUNT')"
                          :error-messages="form.errors.get('BOTS_GAMES_MIN_COUNT')"
                          outlined
                          :hint="$t('Minimum number of bots to play a game during each cycle.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MIN_COUNT')"
                        />

                        <v-text-field
                          v-if="form.BOTS_GAMES_FREQUENCY"
                          v-model.number="form.BOTS_GAMES_MAX_COUNT"
                          :label="$t('Max bots')"
                          :rules="[validationInteger, validationPositiveNumber]"
                          :error="form.errors.has('BOTS_GAMES_MAX_COUNT')"
                          :error-messages="form.errors.get('BOTS_GAMES_MAX_COUNT')"
                          outlined
                          :hint="$t('Maximum number of bots to play a game during each cycle.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MAX_COUNT')"
                        />

                        <v-text-field
                          v-if="form.BOTS_GAMES_FREQUENCY"
                          v-model.number="form.BOTS_GAMES_MIN_BET"
                          :label="$t('Min bet')"
                          :error="form.errors.has('BOTS_GAMES_MIN_BET')"
                          :error-messages="form.errors.get('BOTS_GAMES_MIN_BET')"
                          outlined
                          :suffix="$t('credits')"
                          :hint="$t('Minimum bet a bot is allowed to make.') + ' ' + $t('Leave empty to use the limit specified in the game settings.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MIN_BET')"
                        />

                        <v-text-field
                          v-if="form.BOTS_GAMES_FREQUENCY"
                          v-model.number="form.BOTS_GAMES_MAX_BET"
                          :label="$t('Max bet')"
                          :error="form.errors.has('BOTS_GAMES_MAX_BET')"
                          :error-messages="form.errors.get('BOTS_GAMES_MAX_BET')"
                          outlined
                          :suffix="$t('credits')"
                          :hint="$t('Minimum bet a bot is allowed to make.') + ' ' + $t('Leave empty to use the limit specified in the game settings.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MAX_BET')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model.number="form.BONUSES_SIGN_UP"
                          :label="$t('Sign up bonus')"
                          :error="form.errors.has('BONUSES_SIGN_UP')"
                          :error-messages="form.errors.get('BONUSES_SIGN_UP')"
                          :suffix="$t('credits')"
                          outlined
                        />

                        <v-text-field
                          v-model.number="form.BONUSES_EMAIL_VERIFICATION"
                          :label="$t('Email verification bonus')"
                          :error="form.errors.has('BONUSES_EMAIL_VERIFICATION')"
                          :error-messages="form.errors.get('BONUSES_EMAIL_VERIFICATION')"
                          :suffix="$t('credits')"
                          outlined
                        />

                        <v-row>
                          <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="form.BONUSES_DEPOSIT_THRESHOLD"
                                :label="$t('Threshold')"
                                :prefix="$t('If deposit amount') + ' >= '"
                                :suffix="$t('credits')"
                                :rules="[validationPositiveNumber]"
                                :error="form.errors.has('BONUSES_DEPOSIT_THRESHOLD')"
                                :error-messages="form.errors.get('BONUSES_DEPOSIT_THRESHOLD')"
                                class="text-center"
                                outlined
                            />
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="form.BONUSES_DEPOSIT_PCT"
                                :label="$t('Deposit bonus')"
                                :prefix="$t('give back')"
                                :suffix="$t('% of deposit amount')"
                                :rules="[validationNumeric]"
                                :error="form.errors.has('BONUSES_DEPOSIT_PCT')"
                                :error-messages="form.errors.get('BONUSES_DEPOSIT_PCT')"
                                class="text-center"
                                outlined
                            />
                          </v-col>
                        </v-row>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Facuet') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-row>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model.number="form.BONUSES_FAUCET_AMOUNT"
                              :label="$t('Faucet amount')"
                              :prefix="$t('Allow to claim')"
                              :suffix="$t('credits')"
                              :rules="[validationNonNegativeInteger]"
                              :error="form.errors.has('BONUSES_FAUCET_AMOUNT')"
                              :error-messages="form.errors.get('BONUSES_FAUCET_AMOUNT')"
                              :hint="$t('Set amount to zero to disable faucet.')"
                              persistent-hint
                              class="text-center"
                              outlined
                            />
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model.number="form.BONUSES_FAUCET_INTERVAL"
                              :label="$t('Faucet interval')"
                              :prefix="$t('every')"
                              :suffix="$t('hours')"
                              :rules="[validationPositiveInteger]"
                              :error="form.errors.has('BONUSES_FAUCET_INTERVAL')"
                              :error-messages="form.errors.get('BONUSES_FAUCET_INTERVAL')"
                              class="text-center"
                              outlined
                            />
                          </v-col>
                        </v-row>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Chat rain') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                            v-model="form.BONUSES_RAIN_FREQUENCY"
                            :items="chatRainFrequencies"
                            :label="$t('Chat rain frequency')"
                            :error="form.errors.has('BONUSES_RAIN_FREQUENCY')"
                            :error-messages="form.errors.get('BONUSES_RAIN_FREQUENCY')"
                            outlined
                        />

                        <v-autocomplete
                            v-if="form.BONUSES_RAIN_FREQUENCY"
                            v-model="form.BONUSES_RAIN_USER"
                            :label="$t('User that makes rain in chat')"
                            :items="users"
                            :loading="userSearchInProgress"
                            :search-input.sync="userSearchString"
                            color="primary"
                            hide-selected
                            hide-no-data
                            item-value="id"
                            item-text="name"
                            :placeholder="$t('Search') + '...'"
                            outlined
                        ></v-autocomplete>

                        <template v-if="form.BONUSES_RAIN_FREQUENCY">
                          <h6 class="text-h6 font-weight-thin mb-2">{{ $t('Chat rain payouts') }} ({{ $t('from best to worst') }})</h6>

                          <template v-for="(amount, i) in form.BONUSES_RAIN_AMOUNTS">
                            <v-text-field
                                v-model.number="form.BONUSES_RAIN_AMOUNTS[i]"
                                :label="$t('Amount')"
                                :rules="[validationPositiveInteger]"
                                :error="form.errors.has('BONUSES_RAIN_AMOUNTS')"
                                :error-messages="form.errors.get('BONUSES_RAIN_AMOUNTS')"
                                :prefix="`#${i+1}`"
                                :suffix="$t('credits')"
                                :append-outer-icon="form.BONUSES_RAIN_AMOUNTS.length > 1 ? 'mdi-delete' : ''"
                                @click:append-outer="deleteRainPayout(i)"
                                outlined
                            />
                          </template>

                          <v-btn color="primary" @click="addRainPayout()">
                            {{ $t('Add') }}
                          </v-btn>
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.AFFILIATE_AUTO_APPROVAL_FREQUENCY"
                          :items="commissionsApprovalFrequencies"
                          :label="$t('Commissions auto approval frequency')"
                          :error="form.errors.has('AFFILIATE_AUTO_APPROVAL_FREQUENCY')"
                          :error-messages="form.errors.get('AFFILIATE_AUTO_APPROVAL_FREQUENCY')"
                          outlined
                        />

                        <v-switch
                          v-model="form.AFFILIATE_ALLOW_SAME_IP"
                          :label="$t('Do not validate IP address when creating commissions')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Commissions') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <template v-if="form.AFFILIATE_COMMISSIONS_SIGN_UP">
                          <div v-for="tier in [0,1,2]" :key="'tier-' + tier">
                            <h4 class="subtitle-1 mb-3">{{ $t('Tier {0}', [tier+1]) }}</h4>

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_SIGN_UP[tier]"
                              :label="$t('Referrer sign up bonus')"
                              :rules="[validationInteger, validationNonNegativeNumber]"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_SIGN_UP')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_SIGN_UP')"
                              :suffix="$t('credits')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_GAME_LOSS[tier]"
                              :label="$t('Referrer game loss bonus')"
                              :rules="[validationNonNegativeNumber]"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_GAME_LOSS')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_GAME_LOSS')"
                              :suffix="$t('% of bet amount')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_GAME_WIN[tier]"
                              :label="$t('Referrer game win bonus')"
                              :rules="[validationNonNegativeNumber]"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_GAME_WIN')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_GAME_WIN')"
                              :suffix="$t('% of bet amount')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_DEPOSIT[tier]"
                              :label="$t('Referrer deposit bonus')"
                              :rules="[validationNonNegativeNumber]"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_DEPOSIT')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_DEPOSIT')"
                              :suffix="$t('% of deposit amount')"
                              outlined
                            />
                          </div>
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Google Tag Manager') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.GTM_CONTAINER_ID"
                          :label="$t('Container ID')"
                          :error="form.errors.has('GTM_CONTAINER_ID')"
                          :error-messages="form.errors.get('GTM_CONTAINER_ID')"
                          outlined
                          @keydown="clearFormErrors($event,'GTM_CONTAINER_ID')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Google reCaptcha') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.RECAPTCHA_PUBLIC_KEY"
                          :label="$t('Public key')"
                          :error="form.errors.has('RECAPTCHA_PUBLIC_KEY')"
                          :error-messages="form.errors.get('RECAPTCHA_PUBLIC_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'RECAPTCHA_PUBLIC_KEY')"
                        />

                        <v-text-field
                          v-model="form.RECAPTCHA_SECRET_KEY"
                          :label="$t('Secret key')"
                          :error="form.errors.has('RECAPTCHA_SECRET_KEY')"
                          :error-messages="form.errors.get('RECAPTCHA_SECRET_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'RECAPTCHA_SECRET_KEY')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Pusher') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.PUSHER_APP_ID"
                          :label="$t('Pusher app ID')"
                          :error="form.errors.has('PUSHER_APP_ID')"
                          :error-messages="form.errors.get('PUSHER_APP_ID')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_ID')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_KEY"
                          :label="$t('Pusher app key')"
                          :error="form.errors.has('PUSHER_APP_KEY')"
                          :error-messages="form.errors.get('PUSHER_APP_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_KEY')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_SECRET"
                          :label="$t('Pusher app secret')"
                          :error="form.errors.has('PUSHER_APP_SECRET')"
                          :error-messages="form.errors.get('PUSHER_APP_SECRET')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_SECRET')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_CLUSTER"
                          :label="$t('Pusher app cluster')"
                          :error="form.errors.has('PUSHER_APP_CLUSTER')"
                          :error-messages="form.errors.get('PUSHER_APP_CLUSTER')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_CLUSTER')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Queues') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.QUEUE_CONNECTION"
                          :items="queueDrivers"
                          :label="$t('Queue driver')"
                          :error="form.errors.has('QUEUE_CONNECTION')"
                          :error-messages="form.errors.get('QUEUE_CONNECTION')"
                          :persistent-hint="true"
                          :hint="['database','redis'].indexOf(form.QUEUE_CONNECTION) > -1 ? $t('Make sure to start the queue worker background process, please check the Help page for more info.') : ''"
                          outlined
                        />

                        <template v-if="form.QUEUE_CONNECTION === 'redis'">
                          <v-text-field
                            v-model="form.REDIS_URL"
                            :label="$t('URL')"
                            :error="form.errors.has('REDIS_URL')"
                            :error-messages="form.errors.get('REDIS_URL')"
                            outlined
                            @keydown="clearFormErrors($event,'REDIS_URL')"
                          />

                          <v-text-field
                            v-model="form.REDIS_HOST"
                            :label="$t('Host')"
                            :rules="[validationRequired]"
                            :error="form.errors.has('REDIS_HOST')"
                            :error-messages="form.errors.get('REDIS_HOST')"
                            outlined
                            @keydown="clearFormErrors($event,'REDIS_HOST')"
                          />

                          <v-text-field
                            v-model="form.REDIS_PORT"
                            :label="$t('Port')"
                            :rules="[validationRequired, validationInteger]"
                            :error="form.errors.has('REDIS_PORT')"
                            :error-messages="form.errors.get('REDIS_PORT')"
                            outlined
                            @keydown="clearFormErrors($event,'REDIS_PORT')"
                          />

                          <v-text-field
                            v-model="form.REDIS_DB"
                            :label="$t('Database')"
                            :rules="[validationRequired]"
                            :error="form.errors.has('REDIS_DB')"
                            :error-messages="form.errors.get('REDIS_DB')"
                            outlined
                            @keydown="clearFormErrors($event,'REDIS_DB')"
                          />

                          <v-text-field
                            v-model="form.REDIS_PASSWORD"
                            :label="$t('Password')"
                            :error="form.errors.has('REDIS_PASSWORD')"
                            :error-messages="form.errors.get('REDIS_PASSWORD')"
                            outlined
                            @keydown="clearFormErrors($event,'REDIS_PASSWORD')"
                          />
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Crypto API') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.API_CRYPTO_PROVIDER"
                          :items="cryptoApiProviders"
                          :label="$t('API')"
                          :error="form.errors.has('API_CRYPTO_PROVIDER')"
                          :error-messages="form.errors.get('API_CRYPTO_PROVIDER')"
                          :hint="$t('It is important to refresh the data after changing the API and saving the settings.')"
                          persistent-hint
                          outlined
                          class="mb-5"
                        >
                          <template v-slot:append-outer>
                            <v-btn color="primary" :loading="assetSeederButtonDisabled" :disabled="assetSeederButtonDisabled" @click="runAssetSeeder" class="mt-n2">
                              <v-icon small class="mr-2">
                                mdi-autorenew
                              </v-icon>
                              {{ $t('Refresh') }}
                            </v-btn>
                          </template>
                        </v-select>

                        <v-text-field
                          v-if="form.API_CRYPTO_PROVIDER === 'cryptocompare'"
                          v-model="form.API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY"
                          :label="$t('API key')"
                          :rules="[validationRequired]"
                          :error="form.errors.has('API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY')"
                          :error-messages="form.errors.get('API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY')"
                          outlined
                        >
                          <template v-slot:append>
                            <v-btn small color="primary" href="https://min-api.cryptocompare.com" target="_blank">
                              {{ $t('Get a free key') }}
                            </v-btn>
                          </template>
                        </v-text-field>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel v-for="provider in providers" :key="provider">
                      <v-expansion-panel-header>{{ ucfirst(provider) }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form[providerClientIdVariable(provider)]"
                          :label="$t('Client ID')"
                          :error="form.errors.has(providerClientIdVariable(provider))"
                          :error-messages="form.errors.get(providerClientIdVariable(provider))"
                          outlined
                        />

                        <v-text-field
                          v-model="form[providerClientSecretVariable(provider)]"
                          :label="$t('Client secret')"
                          :error="form.errors.has(providerClientSecretVariable(provider))"
                          :error-messages="form.errors.get(providerClientSecretVariable(provider))"
                          outlined
                        />

                        <v-text-field
                          :value="providerRedirectUrlValue(provider)"
                          :ref="`provider-${provider}`"
                          :label="$t('Redirect URL')"
                          append-icon="mdi-content-copy"
                          outlined
                          readonly
                          @click:append="copyToClipboard($refs[`provider-${provider}`][0])"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                  <template >
                  </template>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                    v-model="form.FORMAT_NUMBER_DECIMAL_SEPARATOR"
                    :items="separators"
                    :label="$t('Decimal separator')"
                    :error="form.errors.has('FORMAT_NUMBER_DECIMAL_SEPARATOR')"
                    :error-messages="form.errors.get('FORMAT_NUMBER_DECIMAL_SEPARATOR')"
                    outlined
                  />

                  <v-select
                    v-model="form.FORMAT_NUMBER_THOUSANDS_SEPARATOR"
                    :items="separators"
                    :label="$t('Thousands separator')"
                    :error="form.errors.has('FORMAT_NUMBER_THOUSANDS_SEPARATOR')"
                    :error-messages="form.errors.get('FORMAT_NUMBER_THOUSANDS_SEPARATOR')"
                    outlined
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Driver') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.MAIL_MAILER"
                          :items="mailDrivers"
                          :label="$t('Mail driver')"
                          :error="form.errors.has('MAIL_MAILER')"
                          :error-messages="form.errors.get('MAIL_MAILER')"
                          outlined
                        />
                        <template v-if="form.MAIL_MAILER === 'smtp'">
                          <v-text-field
                            v-model="form.MAIL_HOST"
                            :label="$t('Host')"
                            :error="form.errors.has('MAIL_HOST')"
                            :error-messages="form.errors.get('MAIL_HOST')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_HOST')"
                          />

                          <v-text-field
                            v-model="form.MAIL_PORT"
                            :label="$t('Port')"
                            :rules="[validationNumeric]"
                            :error="form.errors.has('MAIL_PORT')"
                            :error-messages="form.errors.get('MAIL_PORT')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_PORT')"
                          />

                          <v-text-field
                            v-model="form.MAIL_ENCRYPTION"
                            :label="$t('Encryption')"
                            :error="form.errors.has('MAIL_ENCRYPTION')"
                            :error-messages="form.errors.get('MAIL_ENCRYPTION')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_ENCRYPTION')"
                          />

                          <v-text-field
                            v-model="form.MAIL_USERNAME"
                            :label="$t('User name')"
                            :error="form.errors.has('MAIL_USERNAME')"
                            :error-messages="form.errors.get('MAIL_USERNAME')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_USERNAME')"
                          />

                          <v-text-field
                            v-model="form.MAIL_PASSWORD"
                            :label="$t('Password')"
                            :error="form.errors.has('MAIL_PASSWORD')"
                            :error-messages="form.errors.get('MAIL_PASSWORD')"
                            outlined
                            type="password"
                            @keydown="clearFormErrors($event,'MAIL_PASSWORD')"
                          />

                          <v-text-field
                            v-model="form.MAIL_FROM_ADDRESS"
                            :label="$t('E-mail from address')"
                            :rules="[validationEmail]"
                            :error="form.errors.has('MAIL_FROM_ADDRESS')"
                            :error-messages="form.errors.get('MAIL_FROM_ADDRESS')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_FROM_ADDRESS')"
                          />

                          <v-text-field
                            v-model="form.MAIL_FROM_NAME"
                            :label="$t('E-mail from name')"
                            :error="form.errors.has('MAIL_FROM_NAME')"
                            :error-messages="form.errors.get('MAIL_FROM_NAME')"
                            outlined
                            @keydown="clearFormErrors($event,'MAIL_FROM_NAME')"
                          />
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Notifications') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-expansion-panels>
                          <v-expansion-panel>
                            <v-expansion-panel-header>{{ $t('Admin') }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-text-field
                                v-model="form.NOTIFICATIONS_ADMIN_EMAIL"
                                :label="$t('E-mail')"
                                :error="form.errors.has('NOTIFICATIONS_ADMIN_EMAIL')"
                                :error-messages="form.errors.get('NOTIFICATIONS_ADMIN_EMAIL')"
                                :rules="[validationEmail]"
                                outlined
                                @keydown="clearFormErrors($event,'NOTIFICATIONS_ADMIN_EMAIL')"
                              />

                              <v-switch
                                v-model="form.NOTIFICATIONS_ADMIN_REGISTRATION_ENABLED"
                                :label="$t('Notify when a new user signs up')"
                                color="primary"
                                false-value="false"
                                true-value="true"
                              />

                              <v-row>
                                <v-col>
                                  <v-switch
                                    v-model="form.NOTIFICATIONS_ADMIN_GAME_WIN_ENABLED"
                                    :label="$t('Notify when a user wins a game')"
                                    color="primary"
                                    false-value="false"
                                    true-value="true"
                                  />
                                </v-col>
                                <v-col>
                                  <v-text-field
                                    v-model.number="form.NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD"
                                    :label="$t('Win treshold')"
                                    :error="form.errors.has('NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD')"
                                    :error-messages="form.errors.get('NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD')"
                                    :rules="'' + form.NOTIFICATIONS_ADMIN_GAME_WIN_ENABLED === 'false' ? [] : [validationPositiveInteger]"
                                    :disabled="'' + form.NOTIFICATIONS_ADMIN_GAME_WIN_ENABLED === 'false'"
                                    outlined
                                    :suffix="$t('credits')"
                                    @keydown="clearFormErrors($event,'NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD')"
                                  />
                                </v-col>
                              </v-row>

                              <v-row>
                                <v-col>
                                  <v-switch
                                    v-model="form.NOTIFICATIONS_ADMIN_GAME_LOSS_ENABLED"
                                    :label="$t('Notify when a user loses a game')"
                                    color="primary"
                                    false-value="false"
                                    true-value="true"
                                  />
                                </v-col>
                                <v-col>
                                  <v-text-field
                                    v-model.number="form.NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD"
                                    :label="$t('Loss treshold')"
                                    :error="form.errors.has('NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD')"
                                    :error-messages="form.errors.get('NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD')"
                                    :rules="'' + form.NOTIFICATIONS_ADMIN_GAME_LOSS_ENABLED === 'false' ? [] : [validationPositiveInteger]"
                                    :disabled="'' + form.NOTIFICATIONS_ADMIN_GAME_LOSS_ENABLED === 'false'"
                                    outlined
                                    :suffix="$t('credits')"
                                    @keydown="clearFormErrors($event,'NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD')"
                                  />
                                </v-col>
                              </v-row>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-switch
                    v-model="form.APP_DEBUG"
                    :label="$t('Debug mode')"
                    color="primary"
                    false-value="false"
                    true-value="true"
                  />

                  <v-select
                    v-model="form.APP_LOG_LEVEL"
                    :items="logLevels"
                    :label="$t('Log level')"
                    :error="form.errors.has('APP_LOG_LEVEL')"
                    :error-messages="form.errors.get('APP_LOG_LEVEL')"
                    outlined
                  />

                  <v-select
                    v-model="form.LOG_CHANNEL"
                    :items="logChannels"
                    :label="$t('Log channel')"
                    :error="form.errors.has('LOG_CHANNEL')"
                    :error-messages="form.errors.get('LOG_CHANNEL')"
                    outlined
                  />

                  <v-text-field
                    v-if="form.LOG_CHANNEL === 'daily'"
                    v-model="form.APP_LOG_DAYS"
                    :label="$t('Keep log files for')"
                    :rules="[validationNumeric]"
                    :error="form.errors.has('APP_LOG_DAYS')"
                    :error-messages="form.errors.get('APP_LOG_DAYS')"
                    outlined
                    :suffix="$t('days')"
                    @keydown="clearFormErrors($event,'APP_LOG_DAYS')"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <template v-for="(pkg, id) in packages">
              <v-tab-item v-if="packageComponents[id]" :key="id">
                <component :is="packageComponents[id]" :form="form" @input="mergePackageConfig" />
              </v-tab-item>
            </template>
          </v-tabs>
          <div class="text-right mt-3">
            <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">{{ $t('Save') }}</v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import { mapState } from 'vuex'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import { config } from '~/plugins/config'
import { ucfirst, copyToClipboard } from '~/plugins/utils'
import ColorPicker from '~/components/ColorPicker'
import FileUpload from '~/components/Admin/FileUpload'
import FileEditor from '~/components/Admin/FileEditor'
import axios from 'axios'
import Stars from '../../../components/Backgrounds/Stars'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin', 'config'],

  components: { Stars, FileEditor, FileUpload, ColorPicker },

  mixins: [FormMixin],

  metaInfo () {
    return { title: this.$t('Settings') }
  },

  data () {
    const chatRainUserId = parseInt(config('settings.bonuses.rain.user'), 10)

    return {
      assetSeederButtonDisabled: false,
      colorPickers: [false, false, false, false, false, false, false, false],
      form: new Form({
        // ...Object.keys(this.envMap).reduce((o, key) => ({ ...o, [key]: config(this.envMap[key]) }), {}),
        // ...{ APP_NAME: 'app.name' },
        APP_NAME: config('app.name'),
        LOCALE: config('app.default_locale'),
        THEME_MODE: config('settings.theme.mode'),
        THEME_COLOR_PRIMARY: config('settings.theme.colors.primary'), // $t('Primary color')
        THEME_COLOR_SECONDARY: config('settings.theme.colors.secondary'), // $t('Secondary color')
        THEME_COLOR_ACCENT: config('settings.theme.colors.accent'), // $t('Accent color')
        THEME_COLOR_ERROR: config('settings.theme.colors.error'), // $t('Error color')
        THEME_COLOR_INFO: config('settings.theme.colors.info'), // $t('Info color')
        THEME_COLOR_SUCCESS: config('settings.theme.colors.success'), // $t('Success color')
        THEME_COLOR_WARNING: config('settings.theme.colors.warning'), // $t('Warning color')
        THEME_COLOR_ANCHOR: config('settings.theme.colors.anchor'), // $t('Anchor color')
        THEME_BACKGROUND: config('settings.theme.background'),
        CONTENT_HOME_SLIDER: config('settings.content.home.slider'),
        CONTENT_HOME_GAMES_DISPLAY_STYLE: config('settings.content.home.games.display_style'),
        CONTENT_HOME_GAMES_DISPLAY_COUNT: config('settings.content.home.games.display_count'),
        CONTENT_HOME_GAMES_LOAD_COUNT: config('settings.content.home.games.load_count'),
        CONTENT_HOME_PROVIDER_GAMES_DISPLAY_STYLE: config('settings.content.home.provider_games.display_style'),
        CONTENT_HOME_PROVIDER_GAMES_DISPLAY_COUNT: config('settings.content.home.provider_games.display_count'),
        CONTENT_HOME_PROVIDER_GAMES_LOAD_COUNT: config('settings.content.home.provider_games.load_count'),
        CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES: config('settings.content.home.provider_games.featured_categories'),
        CONTENT_SOCIAL: config('settings.content.social'),
        CONTENT_LEADERBOARD_ENABLED: '' + config('settings.content.leaderboard.enabled'),
        CONTENT_FOOTER_MENU: config('settings.content.footer.menu'),
        BOTS_GAMES_FREQUENCY: config('settings.bots.games.frequency'),
        BOTS_GAMES_MIN_COUNT: config('settings.bots.games.min_count'),
        BOTS_GAMES_MAX_COUNT: config('settings.bots.games.max_count'),
        BOTS_GAMES_MIN_BET: config('settings.bots.games.min_bet'),
        BOTS_GAMES_MAX_BET: config('settings.bots.games.max_bet'),
        INTERFACE_CREDITS_ICON: config('settings.interface.credits_icon'),
        INTERFACE_NAVBAR_VISIBLE: '' + config('settings.interface.navbar.visible'),
        INTERFACE_ONLINE_ENABLED: '' + config('settings.interface.online.enabled'),
        INTERFACE_SYSTEM_BAR_ENABLED: '' + config('settings.interface.system_bar.enabled'),
        CHAT_ENABLED: '' + config('settings.interface.chat.enabled'),
        CHAT_MESSAGE_MAX_LENGTH: parseInt(config('settings.interface.chat.message_max_length'), 10),
        BONUSES_SIGN_UP: parseInt(config('settings.bonuses.sign_up'), 10),
        BONUSES_EMAIL_VERIFICATION: parseInt(config('settings.bonuses.email_verification'), 10),
        BONUSES_FAUCET_AMOUNT: parseInt(config('settings.bonuses.faucet.amount'), 10),
        BONUSES_FAUCET_INTERVAL: parseInt(config('settings.bonuses.faucet.interval'), 10),
        BONUSES_RAIN_FREQUENCY: config('settings.bonuses.rain.frequency'),
        BONUSES_RAIN_USER: chatRainUserId,
        BONUSES_RAIN_AMOUNTS: config('settings.bonuses.rain.amounts'),
        BONUSES_DEPOSIT_THRESHOLD: parseInt(config('settings.bonuses.deposit.threshold'), 10),
        BONUSES_DEPOSIT_PCT: parseFloat(config('settings.bonuses.deposit.pct')),
        AFFILIATE_AUTO_APPROVAL_FREQUENCY: config('settings.affiliate.auto_approval_frequency'),
        AFFILIATE_ALLOW_SAME_IP: '' + config('settings.affiliate.allow_same_ip'),
        AFFILIATE_COMMISSIONS_SIGN_UP: config('settings.affiliate.commissions.sign_up.rates'),
        AFFILIATE_COMMISSIONS_GAME_LOSS: config('settings.affiliate.commissions.game_loss.rates'),
        AFFILIATE_COMMISSIONS_GAME_WIN: config('settings.affiliate.commissions.game_win.rates'),
        AFFILIATE_COMMISSIONS_DEPOSIT: config('settings.affiliate.commissions.deposit.rates'),
        SESSION_LIFETIME: parseInt(config('session.lifetime'), 10),
        EMAIL_VERIFICATION: '' + config('settings.email_verification'),
        GTM_CONTAINER_ID: config('services.gtm.container_id'),
        RECAPTCHA_PUBLIC_KEY: config('services.recaptcha.public_key'),
        RECAPTCHA_SECRET_KEY: config('services.recaptcha.secret_key'),
        PUSHER_APP_ID: config('broadcasting.connections.pusher.app_id'),
        PUSHER_APP_KEY: config('broadcasting.connections.pusher.key'),
        PUSHER_APP_SECRET: config('broadcasting.connections.pusher.secret'),
        PUSHER_APP_CLUSTER: config('broadcasting.connections.pusher.options.cluster'),
        QUEUE_CONNECTION: config('queue.default'),
        REDIS_URL: config('database.redis.default.url'),
        REDIS_HOST: config('database.redis.default.host'),
        REDIS_PORT: config('database.redis.default.port'),
        REDIS_DB: config('database.redis.default.database'),
        REDIS_PASSWORD: config('database.redis.default.password'),
        API_CRYPTO_PROVIDER: config('services.api.crypto.provider'),
        API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY: config('services.api.crypto.providers.cryptocompare.api_key'),
        FORMAT_NUMBER_DECIMAL_SEPARATOR: parseInt(config('settings.format.number.decimal_separator'), 10),
        FORMAT_NUMBER_THOUSANDS_SEPARATOR: parseInt(config('settings.format.number.thousands_separator'), 10),
        APP_DEBUG: '' + config('app.debug'),
        APP_LOGO: config('app.logo'),
        GAMES_PLAYING_CARDS_DECK: config('settings.games.playing_cards.deck'),
        GAMES_PLAYING_CARDS_FRONT_IMAGE: config('settings.games.playing_cards.front_image'),
        GAMES_PLAYING_CARDS_BACK_IMAGE: config('settings.games.playing_cards.back_image'),
        LOG_CHANNEL: config('logging.default'),
        APP_LOG_LEVEL: config(`logging.channels.${config('logging.default')}.level`),
        APP_LOG_DAYS: parseInt(config('logging.channels.daily.days'), 10),
        MAIL_MAILER: config('mail.default'),
        MAIL_HOST: config('mail.mailers.smtp.host'),
        MAIL_PORT: config('mail.mailers.smtp.port'),
        MAIL_FROM_ADDRESS: config('mail.from.address'),
        MAIL_FROM_NAME: config('mail.from.name'),
        MAIL_ENCRYPTION: config('mail.mailers.smtp.encryption'),
        MAIL_USERNAME: config('mail.mailers.smtp.username'),
        MAIL_PASSWORD: config('mail.mailers.smtp.password'),
        NOTIFICATIONS_ADMIN_EMAIL: config('settings.notifications.admin.email'),
        NOTIFICATIONS_ADMIN_REGISTRATION_ENABLED: '' + config('settings.notifications.admin.registration.enabled'),
        NOTIFICATIONS_ADMIN_GAME_WIN_ENABLED: '' + config('settings.notifications.admin.game.win.enabled'),
        NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD: parseInt(config('settings.notifications.admin.game.win.treshold'), 10),
        NOTIFICATIONS_ADMIN_GAME_LOSS_ENABLED: '' + config('settings.notifications.admin.game.loss.enabled'),
        NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD: parseInt(config('settings.notifications.admin.game.loss.treshold'), 10),
        ...Object.keys(config('oauth')).reduce((o, provider) => {
          o[provider.toUpperCase() + '_CLIENT_ID'] = config(`oauth.${provider}.client_id`)
          o[provider.toUpperCase() + '_CLIENT_SECRET'] = config(`oauth.${provider}.client_secret`)
          return o
        }, {})
      }),
      packageComponents: {},
      users: chatRainUserId ? [{ id: chatRainUserId, name: '' + chatRainUserId }] : [],
      userSearchInProgress: false,
      userSearchString: null
    }
  },

  computed: {
    ...mapState('lang', ['locale', 'locales']),
    ...mapState('package-manager', ['packages']),
    gameProvidersPackageEnabled () {
      return this.$store.getters['package-manager/getById']('game-providers')
    },
    backgrounds () {
      return [
        { value: 'Circles', text: this.$t('Circles') },
        { value: 'Squares', text: this.$t('Squares') },
        { value: 'Stars', text: this.$t('Stars') },
        { value: null, text: this.$t('None') }
      ]
    },
    colors () {
      return Object.keys(this.form).filter(v => v.startsWith('THEME_COLOR_'))
    },
    commissionsApprovalFrequencies () {
      return [
        { value: 'daily', text: this.$t('Every day at 00:00') },
        { value: 'weekly', text: this.$t('Every week on Monday at 00:00') },
        { value: 'monthly', text: this.$t('Every first day of month at 00:00') },
        { value: 'never', text: this.$t('Never') }
      ]
    },
    cryptoApiProviders () {
      return Object.keys(config('services.api.crypto.providers'))
    },
    decks () {
      const decks = (config('settings.games.playing_cards.decks') || []).map(deck => ({ value: deck, text: ucfirst(deck) }))
      decks.push({ value: '', text: this.$t('Symbols') })
      return decks
    },
    frequencies () {
      return [
        { value: 'everyMinute', text: this.$t('Every minute') },
        { value: 'everyFiveMinutes', text: this.$t('Every five minutes') },
        { value: 'everyTenMinutes', text: this.$t('Every ten minutes') },
        { value: 'everyFifteenMinutes', text: this.$t('Every fifteen minutes') },
        { value: 'everyThirtyMinutes', text: this.$t('Every thirty minutes') },
        { value: 'hourly', text: this.$t('Every hour') },
        { value: '', text: this.$t('Never') }
      ]
    },
    gameDisplayStyles () {
      return [
        { value: 'block', text: this.$t('Block') },
        { value: 'block2', text: this.$t('Block 2') },
        { value: 'card', text: this.$t('Card') }
      ]
    },
    chatRainFrequencies () {
      return [
        { value: 'everyFifteenMinutes', text: this.$t('Every fifteen minutes') },
        { value: 'everyThirtyMinutes', text: this.$t('Every thirty minutes') },
        { value: 'hourly', text: this.$t('Every hour') },
        { value: 'everyTwoHours', text: this.$t('Every two hours') },
        { value: 'everyThreeHours', text: this.$t('Every three hours') },
        { value: 'everyFourHours', text: this.$t('Every four hours') },
        { value: 'everySixHours', text: this.$t('Every six hours') },
        { value: 'daily', text: this.$t('Every day') },
        { value: 'weekly', text: this.$t('Every week') },
        { value: 'monthly', text: this.$t('Every month') },
        { value: '', text: this.$t('Never') }
      ]
    },
    languages () {
      return Object.keys(this.locales).map(locale => ({ value: locale, text: this.locales[locale].title }))
    },
    logLevels () {
      return ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']
    },
    logChannels () {
      return ['single', 'daily']
    },
    mailDrivers () {
      return [
        { value: 'smtp', text: this.$t('SMTP') },
        { value: 'sendmail', text: this.$t('SendMail') }
      ]
    },
    modes () {
      return [{ value: 'light', text: this.$t('Light') }, { value: 'dark', text: this.$t('Dark') }]
    },
    providers () {
      return Object.keys(config('oauth'))
    },
    pusherEnabled () {
      return this.form.PUSHER_APP_ID && this.form.PUSHER_APP_KEY && this.form.PUSHER_APP_SECRET && this.form.PUSHER_APP_CLUSTER
    },
    queueDrivers () {
      return [
        { value: 'database', text: this.$t('Database') },
        { value: 'redis', text: this.$t('Redis') }
      ]
    },
    separators () {
      return ['.', ',', ' ', '-', ':', ';'].map(v => ({ value: v.charCodeAt(0), text: v === ' ' ? this.$t('Space') : v }))
    }
  },

  watch: {
    async userSearchString (search) {
      // Items have already been requested
      if (this.userSearchInProgress) {
        return
      }

      this.userSearchInProgress = true

      const { data } = await axios.post('/api/admin/users/search', { search })
      this.users = data

      this.userSearchInProgress = false
    }
  },

  created () {
    // Object.keys(this.envMap).forEach(key => {
    //   this.$set(this.form, key, config(this.envMap[key]))
    // })

    // this.form = { ...this.form, ...Object.keys(this.envMap).reduce((o, key) => ({ ...o, [key]: config(this.envMap[key]) }), {}) }

    Object.keys(this.packages).forEach(async packageId => {
      try {
        const module = await import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageId}/resources/js/pages/admin/settings`)
        this.$set(this.packageComponents, packageId, module.default)
      } catch (e) {
        // do nothing
      }
    })
  },

  methods: {
    config,
    addSlide () {
      this.form.CONTENT_HOME_SLIDER.slides.push({
        title: '',
        subtitle: '',
        image: {
          url: ''
        },
        link: {
          title: '',
          url: ''
        }
      })
    },
    deleteSlide (i) {
      this.form.CONTENT_HOME_SLIDER.slides.splice(i, 1)
    },
    addFeaturedCategory () {
      this.form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES.push({ title: 'Title', categories: [] })
    },
    deleteFeaturedCategory (i) {
      this.form.CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES.splice(i, 1)
    },
    addRainPayout () {
      this.form.BONUSES_RAIN_AMOUNTS.push(this.form.BONUSES_RAIN_AMOUNTS[0])
    },
    deleteRainPayout (i) {
      this.form.BONUSES_RAIN_AMOUNTS.splice(i, 1)
    },
    ucfirst (string) {
      return ucfirst(string)
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    },
    openLink (url) {
      window.open(url)
    },
    providerClientIdVariable (provider) {
      return provider.toUpperCase() + '_CLIENT_ID'
    },
    providerClientSecretVariable (provider) {
      return provider.toUpperCase() + '_CLIENT_SECRET'
    },
    providerRedirectUrl (provider) {
      return provider.toUpperCase() + '_REDIRECT_URL'
    },
    providerRedirectUrlValue (provider) {
      return config('app.url') + config(`oauth.${provider}.redirect`)
    },
    colorText (v) {
      const s = v.replace('THEME_COLOR_', '').toLowerCase() + ' color'
      return this.$t(ucfirst(s))
    },
    mergePackageConfig (packageVariables) {
      Object.keys(packageVariables).forEach(key => {
        this.$set(this.form, key, packageVariables[key])
      })
    },
    async runAssetSeeder () {
      this.assetSeederButtonDisabled = true
      const { data } = await axios.post('/api/admin/seeders/asset')
      this.assetSeederButtonDisabled = false

      if (data.success) {
        this.$store.dispatch('message/success', { text: this.$t('Assets successfully updated.') })
      }
    },
    async save () {
      const { data } = await this.form.submit('post', '/api/admin/settings')

      await this.$store.dispatch('config/fetch')

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })
    }
  }
}
</script>
