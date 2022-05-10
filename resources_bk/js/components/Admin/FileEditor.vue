<template>
  <v-form v-model="formIsValid" @submit.prevent="update">
    <v-textarea
      v-model="form.content"
      :label="label || $t('HTML content')"
      :rules="[v => v === null ? true : validationDoesNotContainTags(v, ['html', 'style', 'script', 'head', 'body', 'meta', 'link'])]"
      outlined
    />

    <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
      {{ buttonLabel || $t('Save') }}
    </v-btn>
  </v-form>
</template>

<script>
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'

export default {
  mixins: [FormMixin],

  props: {
    name: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: false,
      default: ''
    },
    buttonLabel: {
      type: String,
      required: false,
      default: ''
    }
  },

  data () {
    return {
      form: new Form({
        content: null
      })
    }
  },

  created () {
    this.fetchContent()
  },

  methods: {
    async fetchContent () {
      const { data: { html } } = await axios.get(`/api/pages/${this.name}`)

      this.form.content = html
    },
    async update () {
      const { data } = await this.form.patch(`/api/admin/files/${this.name}`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })
    }
  }
}
</script>
