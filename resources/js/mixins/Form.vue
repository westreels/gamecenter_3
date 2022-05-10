<script>
import { isNumeric, isInteger } from '~/plugins/utils'

export default {
  data () {
    return {
      formIsValid: null
    }
  },
  methods: {
    // clear form errors
    clearFormErrors ($event, field, form = null) {
      if (!form) {
        form = this.form
      }

      if (form.errors.any()) {
        form.errors.clear(field)
      }
    },
    // field value should be present (non empty)
    validationRequired (value) {
      return !!value || this.$t('This field is required')
    },
    validationNumeric (value) {
      return isNumeric(value) || this.$t('This value should be numeric')
    },
    validationInteger (value) {
      return isInteger(value) || this.$t('This value should be an integer')
    },
    validationPositiveInteger (value) {
      return (isInteger(value) && value > 0) || this.$t('This value should be a positive integer')
    },
    validationNonNegativeInteger (value) {
      return (isInteger(value) && value >= 0) || this.$t('This value should be a non-negative integer')
    },
    validationPositiveNumber (value) {
      return (this.validationNumeric(value) && value > 0) || this.$t('This value should be positive')
    },
    validationNonNegativeNumber (value) {
      return (this.validationNumeric(value) && value >= 0) || this.$t('This value should be non-negative')
    },
    // field should be a valid email
    validationEmail (value) {
      const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      return pattern.test(value) || this.$t('Invalid email')
    },
    // field value should have certain minimum length
    validationMinLength (value, length) {
      return (value && value.length >= length) || this.$t('Min {0} characters', [length])
    },
    // field value should have certain maximum length
    validationMaxLength (value, length) {
      return (value && value.length <= length) || this.$t('Max {0} characters', [length])
    },
    validationMin (value, min) {
      return (this.validationNumeric(value) && value >= min) || this.$t('Min value is {0}', [min])
    },
    validationMax (value, max) {
      return (this.validationNumeric(value) && value <= max) || this.$t('Max value is {0}', [max])
    },
    // field value should have certain maximum length
    validationMaxFileSize (value, size) {
      return (value && value.size / 1048576 <= size) || this.$t('Size should not exceed {0} MB', [size])
    },
    // 2 field values should match
    validationMatch (value, value2) {
      return (value === value2) || this.$t('Password should match')
    },
    validationDoesNotContainTags (value, tags) {
      const errors = []

      tags.forEach(tag => {
        if (value.indexOf('<' + tag) !== -1) {
          // errors.push(this.$t('Tag "{0}" is not allowed', [tag]))
          errors.push(tag)
        }
      })

      const count = errors.length

      return count
        ? (count === 1
          ? this.$t('Tag {0} is not allowed', ['"' + errors[0] + '"'])
          : this.$t('Tags {0} are not allowed', ['"' + errors.join('", "') + '"'])
        )
        : true
    }
  }
}
</script>
