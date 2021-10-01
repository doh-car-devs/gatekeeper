<template>
  <auth-layout>
    <div class="h-full flex flex-col">
      <div class="flex justify-between pb-2 border-b">
        <p class="text-3xl text-gray-600 font-medium">Roles</p>
        <button-primary @click="showRoleForm">
          <div class="flex items-center space-x-1">
            <i class="fas fa-plus text-sm"></i>
            <span>New Role</span>
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
              </form-select>
            </div>
            <button-secondary type="submit" @click="applyFilter">Search</button-secondary>
          </div>
        </form>

        <div class="text-gray-400 mb-4 lg:mb-0 text-center">{{ `Displaying ${navigation.start}-${navigation.end} of ${navigation.total} items.` }}</div>

        <div v-if="navigation.prev_page_url || navigation.next_page_url" class="flex justify-between lg:justify-end lg:items-center lg:space-x-4">
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
        <table v-if="roles" class="w-full">
          <thead>
          <tr class="border-t border-b bg-gray-100">
            <th class="w-12 p-1"></th>
            <th class="w-96 text-left text-gray-600 uppercase p-1">Name</th>
            <th class="w-96 text-left text-gray-600 uppercase p-1">Description</th>
            <th class="w-96 text-left text-gray-600 uppercase p-1">Permissions</th>
            <th class="w-24 text-left text-gray-600 uppercase p-1">Users</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="role in roles" :key="role.id" class="border-t border-b hover:bg-gray-50">
            <td class="px-2 py-1">
              <div class="flex justify-center items-center space-x-2">
                <button-icon v-if="role.name !== 'System Administrator'" @click="showRoleDeleteForm(role)" title="Delete Role"><i class="fas fa-trash text-red-500 hover:text-red-400 focus:text-red-400"></i></button-icon>
                <button-icon v-if="role.name !== 'System Administrator'" @click="showRoleForm(role)" title="Edit Role"><i class="fas fa-edit text-blue-500 hover:text-blue-400 focus:text-blue-400"></i></button-icon>
              </div>
            </td>
            <td class="px-2 py-1">{{ role.name }}</td>
            <td class="px-2 py-1">{{ role.description }}</td>
            <td class="px-2 py-1">
              <div class="flex flex-wrap">
                <span v-for="permission in role.permissions" :key="permission.id" class="text-sm bg-gray-100 px-4 py-1 m-1 rounded-full">
                  {{ permission.name }}
                </span>
              </div>
            </td>
            <td class="px-2 py-1">
              {{ `${role.users_count} Members` }}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <modal v-if="modal.show" :title="modal.title" @close="modal.show = false">
      <component :is="modal.component" @cancel="modal.show = false" @created-role="createdRole" @updated-role="updatedRole" @deleted-role="deletedRole" :role="modal.role"></component>
    </modal>
  </auth-layout>
</template>

<script>
import AuthLayout from "../../components/layouts/AuthLayout";
import ButtonPrimary from "../../components/buttons/ButtonPrimary";
import FormLabel from "../../components/form/FormLabel";
import FormInput from "../../components/form/FormInput";
import FormSelect from "../../components/form/FormSelect";
import ButtonSecondary from "../../components/buttons/ButtonSecondary";
import LinkSecondary from "../../components/links/LinkSecondary";
import ButtonIcon from "../../components/buttons/ButtonIcon";
import Modal from "../../components/overlays/Modal";
import RoleForm from "./RoleForm";
import RoleDeleteForm from "./RoleDeleteForm";

export default {
  name: "RoleList",
  components: {
    Modal,
    RoleForm,
    RoleDeleteForm,
    ButtonIcon,
    LinkSecondary,
    ButtonSecondary,
    FormSelect,
    FormInput,
    FormLabel,
    ButtonPrimary,
    AuthLayout,
  },
  data() {
    return {
      roles: [],
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
        role: null,
      },
    }
  },
  created() {
    this.loadRoles();
  },
  methods: {
    loadRoles() {
      axios.get(`/api/roles?${this.$route.fullPath.substring(this.$route.fullPath.indexOf('?'))}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => {
        this.roles = response.data.data;
        this.navigation.prev_page_url = response.data.prev_page_url;
        this.navigation.next_page_url = response.data.next_page_url;
        this.navigation.start = response.data.from;
        this.navigation.end = response.data.to;
        this.navigation.total = response.data.total;
      })
      .catch(error => {
        this.roles = [];
        this.navigation.prev_page_url = null;
        this.navigation.next_page_url = null;
      });
    },
    applyFilter() {
      this.$router.push({ path: '/roles', query: { ...this.filter, page: 1 } });
    },
    showRoleForm(role) {
      this.modal.show = true;
      this.modal.title = 'Role Information';
      this.modal.component = 'role-form';
      this.modal.role = role;
    },
    showRoleDeleteForm(role) {
      this.modal.show = true;
      this.modal.title = 'Delete Role';
      this.modal.component = 'role-delete-form';
      this.modal.role = role;
    },
    createdRole(role) {
      this.roles = [
        role,
        ...this.roles,
      ];

      this.modal.show = false;
    },
    updatedRole(role) {
      this.roles.some(item => {
        if (item.id === role.id) {
          item.name = role.name;
          item.description = role.description;
          item.permissions = role.permissions;
        }
      });

      this.modal.show = false;
    },
    deletedRole(role_id) {
      this.roles = this.roles.filter(role => role.id !== role_id);
      this.modal.show = false;
    },
  },
}
</script>
