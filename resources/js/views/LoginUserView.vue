<template>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <h1 class="text-center mb-4">Login do Usuário</h1>
          <form @submit.prevent="handleLogin" class="border p-4 rounded shadow-sm bg-light">
            <div class="mb-3">
              <label for="name" class="form-label">Nome</label>
              <input type="text" v-model="name" id="name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" v-model="email" id="email" class="form-control" required />
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
    name: 'LoginUser',
    data() {
      return {
        name: '',
        email: '',
        error: null,
      };
    },
    methods: {
      async handleLogin() {
        try {
          const response = await axios.post('/api/login/user', {
            name: this.name,
            email: this.email,
          });
          localStorage.setItem('authToken', response.data.token);
          localStorage.setItem('userData', JSON.stringify(response.data.user));
          this.$router.push('/');
        } catch (err) {
          this.error = 'Erro de autenticação. Verifique suas credenciais.';
        }
      },
    },
  };
  </script>
  