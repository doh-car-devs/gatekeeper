<template>
  <form @submit.prevent>
    <div class="flex flex-col space-y-4 pt-4">
      <div class="flex flex-col">
        <form-label>Given Name</form-label>
        <form-input type="text" :value="form_data.given_name" @input="form_data.given_name = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('given_name') ? form_errors.given_name[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col">
        <form-label>Middle Name</form-label>
        <form-input type="text" :value="form_data.middle_name" @input="form_data.middle_name = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('middle_name') ? form_errors.middle_name[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col">
        <form-label>Last Name</form-label>
        <form-input type="text" :value="form_data.last_name" @input="form_data.last_name = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('last_name') ? form_errors.last_name[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col">
        <form-label>Name Suffix</form-label>
        <form-input type="text" :value="form_data.name_suffix" @input="form_data.name_suffix = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('name_suffix') ? form_errors.name_suffix[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col relative">
        <form-label>Office</form-label>
        <form-input ref="office_search_keyword" @keyup.esc.prevent="offices = null" @keyup="searchOffice($event)" placeholder="enter keyword to find office..."></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('office_id') ? 'The office field is required.' : '' }}</form-input-error>
        <div v-if="offices" class="absolute top-14 right-0 w-full flex flex-col bg-gray-200 z-10">
          <div v-for="office in offices" @click="selectOffice(office)" class="flex flex-col p-2 cursor-pointer hover:bg-gray-50">
            <span>{{ office.name }}</span>
            <span class="text-sm text-gray-400">{{ office.parent?.name }}</span>
          </div>
        </div>
      </div>

      {{ }}

      <div class="flex flex-col">
        <form-label>Username</form-label>
        <form-input type="text" :value="form_data.username" @input="form_data.username = $event.target.value"></form-input>
        <form-input-error>{{ form_errors.hasOwnProperty('username') ? form_errors.username[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col relative">
        <form-label>Password</form-label>
        <form-input :type="passwordFieldType" :value="form_data.password" @input="form_data.password = $event.target.value" class="pr-8"></form-input>
        <button-icon @click="show_password = !show_password" class="absolute top-6 right-2">
          <i class="fas text-gray-500 hover:text-gray-400 focus:text-gray-400" :class="{ 'fa-eye': show_password, 'fa-eye-slash': !show_password }"></i>
        </button-icon>
        <form-input-error>{{ form_errors.hasOwnProperty('password') ? form_errors.password[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col relative">
        <form-label>Confirm Password</form-label>
        <form-input :type="confirmPasswordFieldType" :value="form_data.confirm_password" @input="form_data.confirm_password = $event.target.value" class="pr-8"></form-input>
        <button-icon @click="show_confirm_password = !show_confirm_password" class="absolute top-6 right-2">
          <i class="fas text-gray-500 hover:text-gray-400 focus:text-gray-400" :class="{ 'fa-eye': show_confirm_password, 'fa-eye-slash': !show_confirm_password }"></i>
        </button-icon>
        <form-input-error>{{ form_errors.hasOwnProperty('confirm_password') ? form_errors.confirm_password[0] : '' }}</form-input-error>
      </div>

      <div class="flex flex-col relative">
        <form-label>Roles</form-label>
        <form-input type="text" ref="role_search_keyword" @keyup="searchRoles($event)" placeholder="enter keyword to find role..."></form-input>
        <div v-if="roles" class="absolute top-14 right-0 w-full max-h-48 flex flex-col bg-gray-200 overflow-y-auto">
          <div v-for="role in roles" @click="addRole(role)" class="flex flex-col p-2 cursor-pointer hover:bg-gray-50">
            <span>{{ role.name }}</span>
            <span class="text-sm text-gray-400">{{ role.description }}</span>
          </div>
        </div>
        <form-input-error>{{ form_errors.hasOwnProperty('roles') ? form_errors.roles[0] : '' }}</form-input-error>
        <div class="flex flex-wrap my-1">
          <span v-for="role in form_data.roles" class="flex items-center space-x-1 text-sm bg-gray-100 px-3 m-1 py-1 rounded">
            <span>{{ role.name }}</span>
            <button v-if="role.name !== 'System Administrator'" type="button" @click="removeRole(role.id)"><i class="fas fa-times text-sm text-gray-600 hover:text-gray-500"></i></button>
          </span>
        </div>
      </div>

      <div class="flex flex-col">
        <button-primary type="submit" @click="saveUser(user.id)">{{ user.id ? 'Update User' : 'Create User' }}</button-primary>
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
  name: "UserForm",
  props: {
    user: Object,
  },
  components: {
    ButtonPrimary,
    ButtonIcon,
    FormInputError,
    FormInput,
    FormLabel,
  },
  data() {
    const old_user = JSON.parse(JSON.stringify(this.user));
    return {
      form_data: {
        given_name: old_user.given_name ?? '',
        middle_name: old_user.middle_name ?? '',
        last_name: old_user.last_name ?? '',
        name_suffix: old_user.name_suffix ?? '',
        office_id: old_user.office_id ?? null,
        office: old_user.office ?? {},
        username: old_user.username ?? '',
        password: '',
        confirm_password: '',
        roles: old_user.roles ?? [],
      },
      offices: null,
      show_password: false,
      show_confirm_password: false,
      roles: null,
      form_errors: {},
      timeout_handler: null,
    }
  },
  mounted() {
    this.$refs.office_search_keyword.$el.value =  this.form_data.office.name;
  },
  methods: {
    searchOffice(event) {
      clearTimeout(this.timeout_handler);
      this.timeout_handler = setTimeout(function () {
        if ((event.keyCode === 8 || (event.keyCode >= 65 && event.keyCode <= 90))) {
          if (event.target.value !== '') {
            this.form_data.office_id = null;
            this.form_data.office = {};

            axios.get(`/api/offices?keyword=${event.target.value}&per_page=5`, {
              headers: {
                Authorization: `Bearer ${this.$store.getters['getToken']}`,
              },
            })
              .then(response => this.offices = response.data.data)
              .catch(error => {
                this.offices = null;
                this.form_data.office_id = null;
              });
          }
        } else {
          this.offices = null;
        }
      }.bind(this), 500);
    },
    selectOffice(office) {
      this.form_data.office_id = office.id;
      this.form_data.office = office;
      this.$refs.office_search_keyword.$el.value = office.name.trim();
      this.offices = null;
    },
    searchRoles(event) {
      clearTimeout(this.timeout_handler);
      this.timeout_handler = setTimeout(function () {
        if (event.target.value !== '' && (event.keyCode === 8 || (event.keyCode >= 65 && event.keyCode <= 90))) {
          axios.get(`/api/role_search?keyword=${event.target.value}&except=${this.form_data.roles.map(role => role.id).join(',')}`, {
            headers: {
              Authorization: `Bearer ${this.$store.getters['getToken']}`,
            },
          })
          .then(response => this.roles = response.data)
          .catch(error => console.log((error.response)));
        } else {
          this.roles = null;
        }
      }.bind(this), 500);
    },
    addRole(role) {
      this.form_data.roles.push(role);
      this.roles = null;
    },
    removeRole(id) {
      this.form_data.roles = this.form_data.roles.filter(role => role.id !== id);
    },
    saveUser(user_id) {
      if (user_id) {
        this.updateUser(user_id);
      } else {
        this.createUser();
      }
    },
    createUser() {
      axios.post('/api/users', {
        ...this.form_data,
        roles: this.form_data.roles.length > 0 ? this.form_data.roles.map(role => role.id).join(',') : '',
      }, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('createdUser', {
        ...this.form_data,
        id: response.data.id,
        status: 'active',
      }))
      .catch(error => {
        const errors = error.response.data;

        if (typeof errors === 'object') {
          this.form_errors = errors;
        }
      });
    },
    updateUser(user_id) {
      axios.put(`/api/users/${user_id}`, {
        ...this.form_data,
        roles: this.form_data.roles.length > 0 ? this.form_data.roles.map(role => role.id).join(',') : '',
      }, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('updatedUser', {
        ...this.form_data,
        id: user_id,
      }))
      .catch(error => {});
    },
  },
  computed: {
    passwordFieldType() {
      return this.show_password ? 'text' : 'password';
    },
    confirmPasswordFieldType() {
      return this.show_confirm_password ? 'text' : 'password';
    },
    fullName() {
      return this.form_data.given_name + this.form_data.middle_name + this.form_data.last_name;
    },
  },
  watch: {
    fullName(val) {
      this.form_data.username = `${this.form_data.given_name} ${this.form_data.middle_name}`
        .match(/\b\w/g)
        .join('')
        .toLowerCase()
        + this.form_data.last_name.replace(/\s+/, '').toLowerCase();
    },
  },
}
</script>
