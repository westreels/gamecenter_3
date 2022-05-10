<template>
  <v-tooltip bottom>
    <template v-slot:activator="{ on }">
      <v-icon size="1em" v-on="on">
        mdi-help-circle-outline
      </v-icon>
    </template>
    <span>
      <slot />
    </span>
  </v-tooltip>
</template>
