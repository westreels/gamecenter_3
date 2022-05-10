<template>
  <v-hover v-slot:default="{ hover }">
    <v-card :elevation="hover ? 12 : 2">
      <router-link :to="{ name: 'prediction', params: { packageId: id } }" class="text-decoration-none">
        <v-img
          :src="banner"
          height="200px"
          class="align-center"
          gradient="to top right, rgba(0,0,0,.1), rgba(0,0,0,.6)"
        >
          <template v-slot>
            <v-card-title class="d-block text-center text-decoration-none">
              {{ name }}
            </v-card-title>
          </template>
          <template v-slot:placeholder>
            <v-skeleton-loader type="image" />
          </template>
        </v-img>
      </router-link>
    </v-card>
  </v-hover>
</template>
<script>
export default {
  props: {
    id: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    banner: {
      type: String,
      required: true
    }
  }
}
</script>
