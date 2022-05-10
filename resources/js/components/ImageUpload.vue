<template>
  <div>
    <v-text-field
      :value="value"
      :label="label"
      append-icon="mdi-paperclip"
      outlined
      readonly
      :error="formImagePropertyName && form.errors.has(formImagePropertyName)"
      :error-messages="formImagePropertyName ? form.errors.get(formImagePropertyName) : ''"
      @click:append="$refs.fileInput.click()"
    >
      <template v-if="preview" v-slot:prepend-inner>
        <v-avatar size="28" tile @click="$refs.fileInput.click()">
          <img :src="preview" :alt="label" />
        </v-avatar>
      </template>
    </v-text-field>

    <input ref="fileInput" type="file" class="d-none" accept="image/*" @change="setImage">
  </div>
</template>

<script>
export default {
  name: 'ImageUpload',

  props: ['label', 'form', 'formImagePropertyName', 'defaultValue', 'defaultUrl'],

  data () {
    return {
      image: null,
      imageBase64: null
    }
  },

  computed: {
    value () {
      return this.image ? this.image.name : (this.defaultValue || this.$t('Upload a new image'))
    },
    preview () {
      return this.imageBase64 || this.defaultUrl
    }
  },

  methods: {
    setImage (event) {
      const image = event.target.files[0]

      // if uploaded file is an image
      if (image && image.type.match(/^image\/.*/)) {
        if (this.formImagePropertyName) {
          this.form.errors.clear(this.formImagePropertyName)
        }

        this.image = image

        var reader = new FileReader()

        // define a callback function to run, when FileReader finishes its job
        reader.onload = e => { this.imageBase64 = e.target.result }

        // Start the reader job - read file as a data url (base64 format)
        reader.readAsDataURL(image)

        this.$emit('change', image)
      }
    }
  }
}
</script>
<style scoped>
.v-avatar {
  cursor: pointer;
}
</style>
