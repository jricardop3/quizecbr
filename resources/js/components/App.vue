<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <!-- Button para alternar o menu em dispositivos móveis -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <!-- Navbar com links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link to="/" class="nav-link" @click="closeNavbar">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/quiz" class="nav-link" @click="closeNavbar">Quiz</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/login-user" class="nav-link" @click="closeNavbar">Login User</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/login-admin" class="nav-link" @click="closeNavbar">Login Admin</router-link>
          </li>
          <li class="nav-item">
            <button @click="handleLogout" class="btn btn-outline-danger btn-sm mt-1">Logout</button>
          </li>
        </ul>
      </div>
    </div>
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
    
    closeNavbar() {
      // Fechar o menu de navegação após a seleção de um item
      const navbarCollapse = document.getElementById('navbarNav');
      const bootstrapCollapse = new bootstrap.Collapse(navbarCollapse, {
        toggle: false // Não altera o estado, apenas fecha
      });
      bootstrapCollapse.hide(); // Fecha o menu
    }
  },
};
</script>

<style scoped>
</style>
