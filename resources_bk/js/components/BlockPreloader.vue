<template>
  <div class="loader">
    <p class="text-center text--primary">
      {{ $t('Loading') }}
    </p>
    <v-progress-linear
      color="primary"
      indeterminate
      rounded
      height="5"
      width="100"
    />
  </div>
</template>
<style lang="scss" scoped>
  .loader {
    width: 150px;
  }
</style>
