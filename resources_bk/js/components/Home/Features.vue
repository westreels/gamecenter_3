<template>
  <v-container class="mt-10">
    <v-divider class="secondary my-10" />
    <v-row class="my-10">
      <v-col cols="12" md="6" lg="3">
        <div class="d-flex align-center justify-center justify-md-start">
          <v-icon color="secondary lighten-3" size="50" class="mr-5">
            mdi-percent
          </v-icon>
          <h5 class="text-h5 font-weight-bold text-capitalize text--secondary text--lighten-3">
            {{ $t('Low house edge') }}
          </h5>
        </div>
      </v-col>
      <v-col cols="12" md="6" lg="3">
        <div class="d-flex align-center justify-center justify-md-start">
          <v-icon color="secondary lighten-3" size="50" class="mr-5">
            mdi-shield-check-outline
          </v-icon>
          <h5 class="text-h5 font-weight-bold text-capitalize text--secondary text--lighten-3">
            {{ $t('Secure payments') }}
          </h5>
        </div>
      </v-col>
      <v-col cols="12" md="6" lg="3">
        <div class="d-flex align-center justify-center justify-md-start">
          <v-icon color="secondary lighten-3" size="50" class="mr-5">
            mdi-bike-fast
          </v-icon>
          <h5 class="text-h5 font-weight-bold text-capitalize text--secondary text--lighten-3">
            {{ $t('Fast payouts') }}
          </h5>
        </div>
      </v-col>
      <v-col cols="12" md="6" lg="3">
        <div class="d-flex align-center justify-center justify-md-start">
          <v-icon color="secondary lighten-3" size="50" class="mr-5">
            mdi-lifebuoy
          </v-icon>
          <h5 class="text-h5 font-weight-bold text-capitalize text--secondary text--lighten-3">
            {{ $t('Support 24 / 7') }}
          </h5>
        </div>
      </v-col>
    </v-row>
    <v-divider class="secondary my-10" />
  </v-container>
</template>
