<template>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <h1 class="text-center mb-4">Login do Administrador</h1>
          <form @submit.prevent="handleLogin" class="border p-4 rounded shadow-sm bg-light">
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" v-model="email" id="email" class="form-control" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Senha</label>
              <input type="password" v-model="password" id="password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <p v-if="error" class="text-danger mt-3">{{ error }}</p>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'LoginAdmin',
    data() {
      return {
        email: '',
        password: '',
        error: null,
      };
    },
    methods: {
      async handleLogin() {
        try {
          const response = await axios.post('/api/login/admin', {
            email: this.email,
            password: this.password,
          });
          localStorage.setItem('authToken', response.data.token);
          localStorage.setItem('userData', JSON.stringify(response.data.user));
          this.$router.push('/admin-dashboard');
        } catch (err) {
          this.error = 'Erro de autenticação. Verifique suas credenciais.';
        }
      },
    },
  };
  </script>
  