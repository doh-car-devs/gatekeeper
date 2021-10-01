import { createRouter, createWebHistory } from "vue-router";
import store from "../store";
import Home from "../views/Home";
import UserList from "../views/users/UserList";
import RoleList from "../views/roles/RoleList";
import PermissionList from "../views/permissions/PermissionList";
import Login from "../views/Login";
import Logout from "../views/Logout";

const routes = [
  {
    path: '/',
    alias: '/home',
    name: 'home',
    component: Home,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/users',
    name: 'users',
    component: UserList,
    meta: {
      requiresAuth: true,
      requiredPermission: 'user:list',
    },
  },
  {
    path: '/roles',
    name: 'roles',
    component: RoleList,
    meta: {
      requiresAuth: true,
      requiredPermission: 'role:list',
    },
  },
  {
    path: '/permissions',
    name: 'permissions',
    component: PermissionList,
    meta: {
      requiresAuth: true,
      requiredPermission: 'permission:list',
    },
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/logout',
    name: 'logout',
    component: Logout,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from) => {
  if (to.meta.requiresAuth) {
    if (store.getters['getToken'] === null
      || (to.path !== '/' && !store.getters['getTokenScope'].includes(to.meta.requiredPermission))) {
      return '/login';
    }
  }
});

export default router;
