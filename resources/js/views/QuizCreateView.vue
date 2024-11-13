<template>
    <div class="container my-5">
        <NavDashboard />
        <div class="card">
            <div class="card-body">
                <h5>Criar Novo Quiz</h5>
                <form @submit.prevent="createQuiz">
                    <!-- Título do Quiz -->
                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">Título do Quiz</label>
                        <input type="text" v-model="quizTitle" class="form-control" required />
                    </div>

                    <!-- Descrição do Quiz -->
                    <div class="mb-3">
                        <label for="quizDescription" class="form-label">Descrição</label>
                        <textarea v-model="quizDescription" class="form-control" required></textarea>
                    </div>

                    <!-- Imagem do Quiz (Opcional) -->
                    <div class="mb-3">
                        <label for="quizImage" class="form-label">Imagem (Opcional)</label>
                        <input type="file" @change="handleImageUpload" class="form-control" />
                    </div>

                    <!-- Botão de Submissão -->
                    <button type="submit" class="btn btn-success">Criar Quiz</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '@/components/NavDashboard.vue';
export default {
    name: 'QuizCreateView',
    components: {
        NavDashboard,
    },
    data() {
        return {
            quizTitle: '', // Título do quiz
            quizDescription: '', // Descrição do quiz
            quizImage: null, // Imagem do quiz
        };
    },
    methods: {
        // Função para lidar com o upload da imagem
        handleImageUpload(event) {
            this.quizImage = event.target.files[0]; // Armazena o arquivo de imagem selecionado
        },

        // Função para criar o quiz
        async createQuiz() {
            try {
                const token = localStorage.getItem('authToken'); // Obtém o token de autenticação
                const formData = new FormData(); // FormData para envio de dados com imagem

                // Adiciona os dados ao FormData
                formData.append('title', this.quizTitle);
                formData.append('description', this.quizDescription);

                if (this.quizImage) {
                    formData.append('image', this.quizImage); // Adiciona a imagem, se selecionada
                }

                // Faz a requisição à API para criar o quiz
                const response = await axios.post('/api/quizzes', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data', // Necessário para envio de arquivos
                    },
                });

                // Exibe sucesso após a criação do quiz
                alert('Quiz criado com sucesso!');
                this.quizTitle = '';
                this.quizDescription = '';
                this.quizImage = null; // Reseta o formulário

            } catch (err) {
                console.error('Erro ao criar quiz:', err);
                alert('Erro ao criar o quiz. Tente novamente.');
            }
        },
    },
};
</script>

<style scoped>
</style>
