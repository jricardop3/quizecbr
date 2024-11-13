<template>
    <div class="container my-5">
      <div class="row">
        <!-- Coluna de saudação e botão de iniciar quiz -->
        <div class="col-12 text-center p-2">
          <h2 class="mb-4">
            Bem-vindo, {{ userName || 'ao Quiz ECBR' }}
          </h2>
          <router-link to="/quiz" class="btn btn-primary">Iniciar Quiz</router-link>
        </div>
  
        <!-- Coluna de ranking Top 10 -->
        <div class="col-12 ranking-section">
          <h3 class="text-start">Top 10</h3>
          <table class="table table-striped mt-3">
            <thead>
              <tr>
                <th>Posição</th>
                <th>Nome</th>
                <th>Pontuação</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in ranking.slice(0, 10)" :key="user.id">
                <td>{{ index + 1 }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.score }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'HomeView',
    data() {
      return {
        userName: null,
        ranking: [],
      };
    },
    mounted() {
      // Recupera o nome do usuário
      const userData = localStorage.getItem('userData');
      if (userData) {
        this.userName = JSON.parse(userData).name;
      }
  
      // Chama a API para obter o ranking
      this.fetchRanking();
    },
    methods: {
      async fetchRanking() {
        try {
          const token = localStorage.getItem('authToken');
          const response = await axios.get('/api/ranking/general', {
            headers: { Authorization: `Bearer ${token}` },
          });
          this.ranking = response.data;
        } catch (error) {
          console.error('Erro ao carregar o ranking:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* Centraliza a seção de saudação e o botão na coluna */
  .text-center {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
  }
  </style>
  