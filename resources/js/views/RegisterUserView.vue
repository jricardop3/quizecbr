<template>
    <div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
      <h1 class="mb-4">Registro de Usuário</h1>
      <form @submit.prevent="handleRegister" class="w-100" style="max-width: 400px;">
        <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input type="text" id="name" v-model="name" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" id="email" v-model="email" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Registrar</button>
        <p v-if="error" class="text-danger">{{ error }}</p>
        <p>Já possui uma conta? <router-link to="/login-user" class="text-decoration-none">Faça login</router-link></p>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'RegisterUser',
    data() {
      return {
        name: '',
        email: '',
        error: null,
      };
    },
    methods: {
      async handleRegister() {
        try {
          const response = await axios.post('/api/register/user', {
            name: this.name,
            email: this.email,
          });
  
          localStorage.setItem('authToken', response.data.token);
          localStorage.setItem('userData', JSON.stringify(response.data.user));
  
          console.log('Registro bem-sucedido:', response.data);
          this.$router.push('/login-user');
        } catch (err) {
          this.error = 'Erro ao registrar. Verifique suas informações.';
        }
      },
    },
  };
  </script>
  
  <style scoped>
  
  </style>
  