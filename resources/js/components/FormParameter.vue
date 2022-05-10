<template>
  <div>
    <v-text-field
      v-if="parameter.type === 'input'"
      :value="value"
      :label="$t(parameter.name)"
      :error="form.errors.has(formKey + '.' + parameter.id)"
      :error-messages="form.errors.get(formKey + '.' + parameter.id)"
      :hint="parameter.description"
      :persistent-hint="!!parameter.description"
      :disabled="disabled"
      outlined
      :class="{ 'mb-4': !!parameter.description }"
      @keydown="clearFormErrors($event, formKey + '.' + parameter.id)"
      @change="change"
    />

    <v-textarea
      v-else-if="parameter.type === 'textarea'"
      :value="value"
      :label="$t(parameter.name)"
      :error="form.errors.has(formKey + '.' + parameter.id)"
      :error-messages="form.errors.get(formKey + '.' + parameter.id)"
      :hint="parameter.description"
      :persistent-hint="!!parameter.description"
      :disabled="disabled"
      outlined
      :class="{ 'mb-4': !!parameter.description }"
      @keydown="clearFormErrors($event, formKey + '.' + parameter.id)"
      @change="change"
    />

    <v-switch
      v-else-if="parameter.type === 'switch'"
      :value="value"
      :label="$t(parameter.name)"
      :error="form.errors.has(formKey + '.' + parameter.id)"
      :error-messages="form.errors.get(formKey + '.' + parameter.id)"
      :hint="parameter.description"
      :persistent-hint="!!parameter.description"
      :disabled="disabled"
      outlined
      :class="{ 'mb-4': !!parameter.description }"
      :false-value="0"
      :true-value="1"
      color="primary"
      @keydown="clearFormErrors($event, formKey + '.' + parameter.id)"
      @change="change"
    />
  </div>
</template>

<script>
import FormMixin from '~/mixins/Form'

export default {
  name: 'FormParameter',

  mixins: [FormMixin],

  props: {
    parameter: {
      type: Object,
      required: true
    },
    value: {
      validator: value => typeof value === 'string' || typeof value === 'number' || value === null,
      required: true
    },
    form: {
      type: Object,
      required: true
    },
    formKey: {
      type: String,
      required: true,
      default: 'parameters'
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  methods: {
    change (event) {
      this.$emit('input', event)
    }
  }
}
</script>
