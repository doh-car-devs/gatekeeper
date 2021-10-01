<template>
  <auth-layout>
    <div class="h-full flex flex-col">
      <div class="flex justify-between items-center pb-2 border-b">
        <p class="text-xl lg:text-3xl text-gray-600 font-medium">Users</p>
        <button-primary @click="showUserModal">
          <div class="flex items-center space-x-1">
            <i class="fas fa-plus text-sm"></i>
            <span>New User</span>
          </div>
        </button-primary>
      </div>

      <div class="flex flex-col lg:flex-row justify-between lg:items-center pb-4 lg:pb-0">
        <form @submit.prevent>
          <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-2 lg:space-y-0 py-4">
            <div class="lg:w-48 flex flex-col">
              <form-label>Keyword</form-label>
              <form-input type="search" :value="filter.keyword" @input="filter.keyword = $event.target.value"></form-input>
            </div>
            <div class="lg:w-48 flex flex-col">
              <form-label>Order By</form-label>
              <form-select :value="filter.sort" @input="filter.sort = $event.target.value" class="px-2 py-1 border rounded">
                <option value="name_up">Name (a-z)</option>
                <option value="name_down">Name (z-a)</option>
                <option value="username_up">Username (a-z)</option>
                <option value="username_down">Username (z-a)</option>
              </form-select>
            </div>
            <button-secondary type="submit" @click="applyFilter">Search</button-secondary>
          </div>
        </form>

        <div class="text-gray-400 mb-4 lg:mb-0 text-center">{{ `Displaying ${navigation.start}-${navigation.end} of ${navigation.total} items.` }}</div>

        <div v-if="navigation.prev_page_url || navigation.next_page_url" class="flex justify-between lg:justify-end space-x-4">
          <link-secondary :to="navigation.prev_page_url" v-if="navigation.prev_page_url">
            <div class="flex items-center space-x-1">
              <i class="fas fa-chevron-left text-sm"></i>
              <span class="text-sm uppercase">Prev</span>
            </div>
          </link-secondary>
          <link-secondary :to="navigation.next_page_url" v-if="navigation.next_page_url">
            <div class="flex items-center space-x-1">
              <span class="text-sm uppercase">Next</span>
              <i class="fas fa-chevron-right text-sm"></i>
            </div>
          </link-secondary>
        </div>
      </div>

      <div class="overflow-auto">
        <table v-if="users" class="w-full">
          <thead>
          <tr class="border-t border-b bg-gray-100">
            <th class="w-12 p-1"></th>
            <th class="w-40 text-left text-gray-600 uppercase p-1">Name</th>
            <th class="w-40 text-left text-gray-600 uppercase p-1">Username</th>
            <th class="w-48 text-left text-gray-600 uppercase p-1">Roles</th>
            <th class="w-48 text-left text-gray-600 uppercase p-1">Office</th>
            <th class="w-12 text-left text-gray-600 uppercase p-1">Status</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id" class="border-t border-b hover:bg-gray-50">
            <td class="px-2 py-1">
              <div class="flex justify-center items-center space-x-2">
                <button-icon @click="showDeactivationModal(user)" v-if="user.status === 'active' && user.username !== 'admin'" title="Deactivate User"><i class="fas fa-lock text-red-500 hover:text-red-400 focus:text-red-400"></i></button-icon>
                <button-icon @click="showActivationModal(user)" v-else-if="user.status === 'inactive' && user.username !== 'admin'" title="Activate User"><i class="fas fa-unlock text-green-500 hover:text-green-400 focus:text-green-400"></i></button-icon>
                <button-icon @click="showUserModal(user)" v-if="user.username !== 'admin'" title="Edit User"><i class="fas fa-user-edit text-blue-500 hover:text-blue-400 focus:text-blue-400"></i></button-icon>
              </div>
            </td>
            <td class="px-2 py-1">{{ `${user.last_name}, ${user.given_name} ${user.middle_name ?? ''} ${user.name_suffix ?? ''}` }}</td>
            <td class="px-2 py-1">{{ user.username }}</td>
            <td class="px-2 py-1">
              <div class="flex flex-wrap">
                <span v-for="role in user.roles" :key="role.id" class="text-sm bg-gray-100 px-4 py-1 m-1 rounded-full">
                  {{ role.name }}
                </span>
              </div>
            </td>
            <td class="px-2 py-1">{{ user.office.name }}</td>
            <td class="capitalize px-2 py-1">{{ user.status }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <modal v-if="modal.show" :title="modal.title" @close="modal.show = false">
      <component :is="modal.component" @created-user="createdUser" @updated-user="updatedUser" @deactivated-user="deactivatedUser" @activated-user="activatedUser" :user="modal.user" @cancel="modal.show = false"></component>
    </modal>
  </auth-layout>
</template>

<script>
import AuthLayout from "../../components/layouts/AuthLayout";
import ButtonPrimary from "../../components/buttons/ButtonPrimary";
import ButtonSecondary from "../../components/buttons/ButtonSecondary";
import FormInput from "../../components/form/FormInput";
import FormSelect from "../../components/form/FormSelect";
import FormLabel from "../../components/form/FormLabel";
import LinkSecondary from "../../components/links/LinkSecondary";
import Modal from "../../components/overlays/Modal";
import FormInputError from "../../components/form/FormInputError";
import ButtonIcon from "../../components/buttons/ButtonIcon";
import UserForm from "./UserForm";
import UserDeactivateForm from "./UserDeactivateForm";
import UserActivateForm from "./UserActivateForm";

export default {
  name: "UserList",
  components: {
    UserForm,
    UserDeactivateForm,
    UserActivateForm,
    ButtonIcon,
    FormInputError,
    Modal,
    LinkSecondary,
    FormLabel,
    FormSelect,
    FormInput,
    ButtonSecondary,
    ButtonPrimary,
    AuthLayout,
  },
  data() {
    return {
      users: null,
      filter: {
        keyword: this.$route.query.keyword ?? '',
        sort: this.$route.query.sort ?? 'name_up',
        page: this.$route.query.page ?? 1,
        per_page: this.$route.query.per_page ?? 25,
      },
      navigation: {
        prev_page_url: null,
        next_page_url: null,
        start: '',
        end: '',
        total: '',
      },
      modal: {
        show: false,
        title: '',
        component: '',
        user: null,
      },
    }
  },
  created() {
    this.loadUsers();
  },
  methods: {
    loadUsers() {
      axios.get(`/api/users?${this.$route.fullPath.substring(this.$route.fullPath.indexOf('?'))}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
        .then(response => {
          this.users = response.data.data;
          this.navigation.prev_page_url = response.data.prev_page_url;
          this.navigation.next_page_url = response.data.next_page_url;
          this.navigation.start = response.data.from;
          this.navigation.end = response.data.to;
          this.navigation.total = response.data.total;
        })
        .catch(error => {
          this.users = null;
          this.navigation.prev_page_url = null;
          this.navigation.next_page_url = null;
        });
    },
    applyFilter() {
      this.$router.push({ path: '/users', query: { ...this.filter, page: 1 } });
    },
    showUserModal(user) {
      this.modal.show = true;
      this.modal.title = 'User Information';
      this.modal.component = 'user-form';
      this.modal.user = user;
    },
    showDeactivationModal(user) {
      this.modal.show = true;
      this.modal.title = 'Deactivate User';
      this.modal.component = 'user-deactivate-form';
      this.modal.user = user;
    },
    showActivationModal(user) {
      this.modal.show = true;
      this.modal.title = 'Activate User';
      this.modal.component = 'user-activate-form';
      this.modal.user = user;
    },
    createdUser(user) {
      this.users = [
        user,
        ...this.users,
      ];

      this.modal.show = false;
    },
    updatedUser(user) {
      this.users.some(item => {
        if (item.id === user.id) {
          item.given_name = user.given_name;
          item.middle_name = user.middle_name;
          item.last_name = user.last_name;
          item.name_suffix = user.name_suffix;
          item.username = user.username;
          item.office = user.office;
          item.roles = user.roles;
          return true;
        }
      });

      this.modal.show = false;
    },
    deactivatedUser(user_id) {
      this.users.some(item => {
        if (item.id === user_id) {
          item.status = 'inactive';
          return true;
        }
      });

      this.modal.show = false;
    },
    activatedUser(user_id) {
      this.users.some(item => {
        if (item.id === user_id) {
          item.status = 'active';
          return true;
        }
      });

      this.modal.show = false;
    },
  },
}
</script>
