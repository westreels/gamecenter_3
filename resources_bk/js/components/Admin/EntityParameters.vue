<template>
  <div>
    <v-card
      v-for="(parameter, index) in value"
      :key="index"
      outlined
      class="mb-5"
    >
      <v-card-title class="font-weight-thin mb-3">
        {{ $t('Parameter {0}', [parameter.id]) }}
      </v-card-title>
      <v-card-text>
        <v-select
          v-model="parameters[index].type"
          :items="parameterTypes"
          :label="$t('Type')"
          outlined
          @change="change"
        />

        <v-text-field
          v-model="parameters[index].name"
          :label="$t('Name')"
          :rules="[validationRequired]"
          outlined
          @change="change"
        />

        <v-text-field
          v-model="parameters[index].description"
          :label="$t('Description')"
          outlined
          @change="change"
        />

        <v-text-field
          v-model="parameters[index].validation"
          :label="$t('Validation rules')"
          :hint="$t('Should be compatible with Laravel validation rules syntax: {0}', ['https://laravel.com/docs/6.x/validation#available-validation-rules'])"
          :persistent-hint="true"
          outlined
          class="mb-5"
          @change="change"
        />

        <v-switch
          v-if="parameters[index].type === 'switch'"
          v-model="parameters[index].default"
          :label="$t('Default value')"
          :false-value="0"
          :true-value="1"
          color="primary"
          @change="change"
        />

        <v-text-field
          v-else
          v-model="parameters[index].default"
          :label="$t('Default value')"
          outlined
          :hide-details="true"
          @change="change"
        />
      </v-card-text>
      <v-card-actions>
        <v-btn text color="primary" @click="removeParameter(index)">
          {{ $t('Remove') }}
        </v-btn>
      </v-card-actions>
    </v-card>

    <v-row>
      <v-col cols="12" lg="6">
        <v-text-field
          v-model="parameterName"
          :label="$t('Parameter name')"
          outlined
          :hide-details="true"
          append-icon="mdi-plus"
          @click:append="addParameter"
        />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import FormMixin from '~/mixins/Form'

export default {
  name: 'EntityParameters',

  mixins: [FormMixin],

  props: {
    value: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      parameterName: '',
      parameters: this.value
    }
  },

  computed: {
    parameterTypes () {
      return ['input', 'textarea', 'switch']
    }
  },

  methods: {
    addParameter () {
      const id = this.parameterName.replace(/\s/g, '_').toLowerCase()

      if (this.parameterName === '') {
        return false
      }

      const parameter = {
        id,
        type: 'input',
        name: this.parameterName,
        description: '',
        validation: '',
        default: ''
      }

      this.parameters.push(parameter)
      this.parameterName = ''
    },
    removeParameter (index) {
      this.parameters.splice(index, 1)
    },
    change (event) {
      this.$emit('input', this.parameters)
    }
  }
}
</script>
