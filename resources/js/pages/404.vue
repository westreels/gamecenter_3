<template>
  <v-container class="fill-height" fluid>
    <v-layout align-center>
      <v-flex text-center>
        <h1 class="display-3 primary--text">
          {{ $t('Error {0}', [404]) }}
        </h1>
        <p class="my-5">
          {{ $t('This page does not exist') }}
        </p>
        <v-btn :to="{ name: 'home' }" replace outlined color="primary">
          {{ $t('Home') }}
        </v-btn>
      </v-flex>
    </v-layout>
  </v-container>
</template>
