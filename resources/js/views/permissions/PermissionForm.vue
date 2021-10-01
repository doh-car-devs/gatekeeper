<template>
  <form @submit.prevent>
    <div class="flex flex-col space-y-4 pt-4">
      <div class="flex flex-col">
        <form-label>Name</form-label>
        <form-input type="text" :value="form_data.name" @input="form_data.name = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('name') ? form_errors.name[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col">
        <form-label>Description</form-label>
        <form-input type="text" :value="form_data.description" @input="form_data.description = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('description') ? form_errors.description[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col">
        <button-primary type="submit" @click="savePermission(permission.id)">{{ permission.id ? 'Update Permission' : 'Create Permission' }}</button-primary>
      </div>
    </div>
  </form>
</template>

<script>
import FormLabel from "../../components/form/FormLabel";
import FormInput from "../../components/form/FormInput";
import FormInputError from "../../components/form/FormInputError";
import ButtonIcon from "../../components/buttons/ButtonIcon";
import ButtonPrimary from "../../components/buttons/ButtonPrimary";

export default {
  name: "PermissionForm",
  props: {
    permission: Object,
  },
  components: {
    ButtonPrimary,
    ButtonIcon,
    FormInputError,
    FormInput,
    FormLabel,
  },
  data() {
    const old_permission = this.permission ? JSON.parse(JSON.stringify(this.permission)) : null;

    return {
      form_data: {
        name: old_permission.name ?? '',
        description: old_permission.description ?? '',
      },
      form_errors: {},
    }
  },
  methods: {
    savePermission(user_id) {
      if (user_id) {
        this.updatePermission(user_id);
      } else {
        this.createPermission();
      }
    },
    createPermission() {
      axios.post('/api/permissions', this.form_data, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('createdPermission', response.data))
      .catch(error => {
        const errors = error.response.data;

        if (typeof errors === 'object') {
          this.form_errors = errors;
        }
      });
    },
    updatePermission(user_id) {
      axios.put(`/api/permissions/${user_id}`, this.form_data, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('updatedPermission', {
        ...this.form_data,
        id: this.permission.id,
      }))
      .catch(error => {
        const errors = error.response.data;

        if (typeof errors === 'object') {
          this.form_errors = errors;
        }
      });
    },
  },
}
</script>
