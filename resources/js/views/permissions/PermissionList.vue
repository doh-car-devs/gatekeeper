<template>
  <auth-layout>
    <div class="h-full flex flex-col">
      <div class="flex justify-between pb-2 border-b">
        <p class="text-3xl text-gray-600 font-medium">Permissions</p>
        <button-primary @click="showPermissionForm">
          <div class="flex items-center space-x-1">
            <i class="fas fa-plus text-sm"></i>
            <span>New Permission</span>
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
        <table v-if="permissions" class="w-full">
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
          <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50">
            <td class="px-2 py-1">
              <div class="flex justify-center items-center space-x-2">
                <button-icon v-if="(/^(user|role|permission):/i).test(permission.name) === false" @click="showPermissionDeleteForm(permission)" title="Delete Permission"><i class="fas fa-trash text-red-500 hover:text-red-400 focus:text-red-400"></i></button-icon>
                <button-icon v-if="(/^(user|role|permission):/i).test(permission.name) === false" @click="showPermissionForm(permission)" title="Edit Permission"><i class="fas fa-edit text-blue-500 hover:text-blue-400 focus:text-blue-400"></i></button-icon>
              </div>
            </td>
            <td class="px-2 py-1">{{ permission.name }}</td>
            <td class="px-2 py-1">{{ permission.description }}</td>
            <td class="px-2 py-1">
              <div class="flex flex-wrap">
                <span v-for="permission in permission.permissions" :key="permission.id" class="text-sm bg-gray-100 px-4 py-1 m-1 rounded-full">
                  {{ permission.permission }}
                </span>
              </div>
            </td>
            <td class="px-2 py-1">
              {{ `${permission.roles_count} Members` }}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <modal v-if="modal.show" :title="modal.title" @close="modal.show = false">
      <component :is="modal.component" @cancel="modal.show = false" @created-permission="createdPermission" @updated-permission="updatedPermission" @deleted-permission="deletedPermission" :permission="modal.permission"></component>
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
import PermissionForm from "./PermissionForm";
import PermissionDeleteForm from "./PermissionDeleteForm";

export default {
  name: "PermissionList",
  components: {
    Modal,
    PermissionForm,
    PermissionDeleteForm,
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
      permissions: null,
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
        permission: null,
      },
    }
  },
  created() {
    this.loadPermissions();
  },
  methods: {
    loadPermissions() {
      axios.get(`/api/permissions?${this.$route.fullPath.substring(this.$route.fullPath.indexOf('?'))}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => {
        this.permissions = response.data.data;
        this.navigation.prev_page_url = response.data.prev_page_url;
        this.navigation.next_page_url = response.data.next_page_url;
        this.navigation.start = response.data.from;
        this.navigation.end = response.data.to;
        this.navigation.total = response.data.total;
      })
      .catch(error => {
        this.permissions = [];
        this.navigation.prev_page_url = null;
        this.navigation.next_page_url = null;
      });
    },
    applyFilter() {
      this.$router.push({ path: '/permissions', query: { ...this.filter, page: 1 } });
    },
    showPermissionForm(permission) {
      this.modal.show = true;
      this.modal.title = 'Permission Information';
      this.modal.component = 'permission-form';
      this.modal.permission = permission;
    },
    showPermissionDeleteForm(permission) {
      this.modal.show = true;
      this.modal.title = 'Delete Permission';
      this.modal.component = 'permission-delete-form';
      this.modal.permission = permission;
    },
    createdPermission(permission) {
      axios.get(`/api/permissions/${permission.id}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => {
        this.permissions = [
          response.data,
          ...this.permissions,
        ];
      })
      .catch(error => {});

      this.modal.show = false;
    },
    updatedPermission(permission) {
      this.permissions.some(item => {
        if (item.id === permission.id) {
          item.name = permission.name;
          item.description = permission.description;
        }
      });

      this.modal.show = false;
    },
    deletedPermission(permission_id) {
      this.permissions = this.permissions.filter(permission => permission.id !== permission_id);
      this.modal.show = false;
    },
  },
}
</script>
