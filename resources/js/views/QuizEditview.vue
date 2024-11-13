<template>
    <div class="container my-5">
        <NavDashboard />
        <div class="card">
            <div class="card-body">
                <h5>Editar Quiz</h5>
                
                <!-- Selecione o quiz para edição -->
                <div class="mb-3">
                    <label for="quizSelect" class="form-label">Selecione um Quiz</label>
                    <select v-model="selectedQuizId" @change="fetchQuizData" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option v-for="quiz in quizzes" :key="quiz.id" :value="quiz.id">{{ quiz.title }}</option>
                    </select>
                </div>

                <!-- Formulário de edição do quiz -->
                <div v-if="selectedQuizId">
                    <form @submit.prevent="updateQuiz">
                        <div class="mb-3">
                            <label for="quizTitle" class="form-label">Título do Quiz</label>
                            <input type="text" v-model="quizTitle" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label for="quizDescription" class="form-label">Descrição</label>
                            <textarea v-model="quizDescription" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="quizImage" class="form-label">Imagem (Opcional)</label>
                            <input type="file" @change="handleImageUpload" class="form-control" />
                        </div>

                        <button type="submit" class="btn btn-warning">Atualizar Quiz</button>
                        <!-- Botão de exclusão -->
                        <button type="button" @click="deleteQuiz" class="btn btn-danger ms-2">Excluir Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '@/components/NavDashboard.vue';

export default {
    name: 'QuizEditView',
    components: {
        NavDashboard,
    },
    data() {
        return {
            quizzes: [], // Lista de quizzes
            selectedQuizId: null, // ID do quiz selecionado
            quizTitle: '', // Título do quiz a ser editado
            quizDescription: '', // Descrição do quiz a ser editada
            quizImage: null, // Imagem do quiz
        };
    },
    methods: {
        // Função para carregar todos os quizzes
        async fetchQuizzes() {
            try {
                const token = localStorage.getItem('authToken');
                const response = await axios.get('/api/quizzes', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.quizzes = response.data;
            } catch (error) {
                console.error('Erro ao buscar quizzes:', error);
            }
        },

        // Função para carregar os dados do quiz selecionado
        async fetchQuizData() {
            if (!this.selectedQuizId) return;

            try {
                const token = localStorage.getItem('authToken');
                const response = await axios.get(`/api/quizzes/${this.selectedQuizId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                // Preenche os campos com os dados do quiz selecionado
                this.quizTitle = response.data.title;
                this.quizDescription = response.data.description;
                this.quizImage = null; // Reseta a imagem
            } catch (error) {
                console.error('Erro ao carregar o quiz:', error);
            }
        },

        // Função para lidar com o upload da imagem
        handleImageUpload(event) {
            this.quizImage = event.target.files[0];
        },

        // Função para atualizar o quiz
        async updateQuiz() {
            try {
                const token = localStorage.getItem('authToken');
                const formData = new FormData();
                
                formData.append('_method', 'PUT'); // Para Laravel interpretar como atualização
                formData.append('title', this.quizTitle);
                formData.append('description', this.quizDescription);

                if (this.quizImage) {
                    formData.append('image', this.quizImage); // Adiciona a imagem, se selecionada
                }

                await axios.post(`/api/quizzes/${this.selectedQuizId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });

                alert('Quiz atualizado com sucesso!');
                this.selectedQuizId = null;
                this.quizTitle = '';
                this.quizDescription = '';
                this.quizImage = null;
            } catch (error) {
                console.error('Erro ao atualizar quiz:', error);
                alert('Erro ao atualizar o quiz. Tente novamente.');
            }
        },

        // Função para excluir o quiz
        async deleteQuiz() {
            try {
                const token = localStorage.getItem('authToken');
                await axios.delete(`/api/quizzes/${this.selectedQuizId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                alert('Quiz excluído com sucesso!');
                // Resetando os campos após a exclusão
                this.selectedQuizId = null;
                this.quizTitle = '';
                this.quizDescription = '';
                this.quizImage = null;
            } catch (error) {
                console.error('Erro ao excluir o quiz:', error);
                alert('Erro ao excluir o quiz. Tente novamente.');
            }
        },
    },
    mounted() {
        this.fetchQuizzes(); // Carrega todos os quizzes ao montar a view
    },
};
</script>


<style scoped>
</style>
