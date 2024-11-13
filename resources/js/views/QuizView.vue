<template>
    <div class="container my-5">
      <h1 class="text-center">Quiz</h1>
      <p class="text-center">Escolha um quiz para iniciar!</p>
      <div class="row">
        <div v-for="quiz in quizzes" :key="quiz.id" class="col-12 col-md-4 mb-4">
          <div class="card h-100">
            <!-- Adiciona router-link na imagem para navegar para AnswerQuizView passando o quizId e userId -->
            <router-link :to="{ name: 'AnswerQuiz', params: { id: quiz.id, userId: userId } }">
              <img :src="`/storage/${quiz.image}`" class="card-img-top img-fluid" alt="Imagem do Quiz" style="width: 100%; height: 30vh;" />
            </router-link>
            <div class="card-body text-center">
              <!-- Adiciona router-link no título para navegar para AnswerQuizView passando o quizId e userId -->
              <router-link :to="{ name: 'AnswerQuiz', params: { id: quiz.id, userId: userId } }" class="text-decoration-none">
                <h5 class="card-title">{{ quiz.title }}</h5>
              </router-link>
              <p class="card-text">{{ quiz.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'QuizView',
    data() {
      return {
        quizzes: [],
        userId: null, // Armazena o userId localmente
      };
    },
    methods: {
      async fetchQuizzes() {
        try {
          const token = localStorage.getItem('authToken');
          
          // Obtém userData do localStorage e extrai o userId
          const userData = JSON.parse(localStorage.getItem('userData'));
          this.userId = userData?.id; // Extrai o userId de userData
          console.log('User ID:', this.userId);
  
          const response = await axios.get('/api/quizzes', {
            headers: { Authorization: `Bearer ${token}` },
          });
          
          this.quizzes = response.data;
        } catch (error) {
          console.error('Erro ao carregar quizzes:', error);
        }
      },
    },
    mounted() {
      this.fetchQuizzes();
    },
  };
  </script>
  
  