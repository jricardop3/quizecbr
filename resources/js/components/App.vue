<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <ul class="navbar-nav">
        <li class="nav-item"><router-link to="/" class="nav-link">Home</router-link></li>
        <li class="nav-item"><router-link to="/login-user" class="nav-link">Login User</router-link></li>
        <li class="nav-item"><router-link to="/login-admin" class="nav-link">Login Admin</router-link></li>
        <button @click="handleLogout" class="btn btn-outline-danger btn-sm mt-1">Logout</button>
      </ul>
    </nav>
    <router-view />
  </template>
  
  <script>
  export default {
    name: 'App',
  methods: {
    handleLogout() {
      // Remove o token e dados do usuário ao fazer logout
      localStorage.removeItem('authToken');
      localStorage.removeItem('userData');
      this.$router.push('/login-user'); // Redireciona para a página de login
    },
  },
};
  </script>
  
  <style scoped>

  </style>
  