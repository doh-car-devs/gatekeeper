import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";
import * as Cookies from "js-cookie";

const store = createStore({
  state: {
    token: null,
    token_scope: null,
    user: null,
  },
  getters: {
    getToken(state) {
      return state.token;
    },
    getTokenScope(state) {
      return state.token_scope;
    },
    getUser(state) {
      return state.user;
    },
  },
  mutations: {
    setToken(state, token) {
      state.token = token;
    },
    setTokenScope(state, scope) {
      state.token_scope = scope;
    },
    setUser(state, user) {
      state.user = user;
    },
  },
  actions: {
    setToken({ commit }, token) {
      commit('setToken', token);
    },
    setTokenScope({ commit }, scope) {
      commit('setTokenScope', scope);
    },
    setUser({ commit }, user) {
      commit('setUser', user);
    },
  },
  plugins: [
    createPersistedState({
      storage: {
        getItem: (key) => Cookies.get(key),
        setItem: (key, value) => Cookies.set(key, value, {
          expires: 1/6,
          secure: false,
        }),
        removeItem: (key) => Cookies.remove(key),
      },
    }),
  ],
});

export default store;
