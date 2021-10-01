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

      <div class="flex flex-col relative">
        <form-label>Permissions</form-label>
        <form-input type="text" ref="permission_search_keyword" @keyup="searchPermissions($event)" placeholder="enter keyword to find permission..."></form-input>
        <div v-if="permissions" class="absolute top-14 right-0 w-full max-h-48 flex flex-col bg-gray-200 overflow-y-auto">
          <div v-for="permission in permissions" @click="addPermission(permission)" class="flex flex-col p-2 cursor-pointer hover:bg-gray-50">
            <span>{{ permission.name }}</span>
            <span class="text-sm text-gray-400">{{ permission.description }}</span>
          </div>
        </div>
        <form-input-error>{{ form_errors.hasOwnProperty('permissions') ? form_errors.permissions[0] : '' }}</form-input-error>
        <div class="flex flex-wrap my-1">
          <span v-for="permission in form_data.permissions" class="flex items-center space-x-1 text-sm bg-gray-100 px-3 m-1 py-1 rounded">
            <span>{{ permission.name }}</span>
            <button type="button" @click="removePermission(permission.id)"><i class="fas fa-times text-sm text-gray-600 hover:text-gray-500"></i></button>
          </span>
        </div>
      </div>

      <div class="flex flex-col">
        <button-primary type="submit" @click="saveRole(role.id)">{{ role.id ? 'Update Role' : 'Create Role' }}</button-primary>
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
  name: "RoleForm",
  props: {
    role: Object,
  },
  components: {
    ButtonPrimary,
    ButtonIcon,
    FormInputError,
    FormInput,
    FormLabel,
  },
  data() {
    const old_role = this.role ? JSON.parse(JSON.stringify(this.role)) : null;

    return {
      form_data: {
        name: old_role.name ?? '',
        description: old_role.description ?? '',
        permissions: old_role.permissions ?? [],
      },
      permissions: null,
      form_errors: {},
    }
  },
  methods: {
    searchPermissions(event) {
      if (event.target.value !== '' && (event.keyCode === 8 || (event.keyCode >= 65 && event.keyCode <= 90))) {
        axios.get(`/api/permission_search?keyword=${event.target.value}&except=${this.form_data.permissions.map(permission => permission.id).join(',')}`, {
          headers: {
            Authorization: `Bearer ${this.$store.getters['getToken']}`,
          },
        })
        .then(response => this.permissions = response.data)
        .catch(error => this.permissions = null);
      } else {
        this.permissions = null;
      }
    },
    addPermission(permission) {
      this.form_data.permissions.push(permission);
      this.permissions = null;
    },
    removePermission(id) {
      this.form_data.permissions = this.form_data.permissions.filter(permission => permission.id !== id);
    },
    saveRole(user_id) {
      if (user_id) {
        this.updateRole(user_id);
      } else {
        this.createRole();
      }
    },
    createRole() {
      axios.post('/api/roles', {
        ...this.form_data,
        permissions: this.form_data.permissions.length > 0 ? this.form_data.permissions.map(permission => permission.id).join(',') : '',
      }, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('createdRole', {
        ...this.form_data,
        id: response.data.id,
        users_count: 0,
      }))
      .catch(error => {
        const errors = error.response.data;

        if (typeof errors === 'object') {
          this.form_errors = errors;
        }
      });
    },
    updateRole(user_id) {
      axios.put(`/api/roles/${user_id}`, {
        ...this.form_data,
        permissions: this.form_data.permissions.length > 0 ? this.form_data.permissions.map(permission => permission.id).join(',') : '',
      }, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('updatedRole', response.data))
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
