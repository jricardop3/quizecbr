<template>
    <div class="container my-5">
        <NavDashboard />
        <div class="card-body">
            <h5>Criar Nova Pergunta</h5>
            
            <!-- Selecione o quiz para adicionar a pergunta -->
            <div class="mb-3">
                <label for="quizSelect" class="form-label">Selecione um Quiz</label>
                <select v-model="selectedQuizId" @change="fetchQuizData" class="form-select" required>
                    <option value="">Selecione...</option>
                    <option v-for="quiz in quizzes" :key="quiz.id" :value="quiz.id">{{ quiz.title }}</option>
                </select>
            </div>

            <!-- Formulário para criação da pergunta -->
            <div v-if="selectedQuizId">
                <form @submit.prevent="createQuestion">
                    <div class="mb-3">
                        <label for="questionText" class="form-label">Texto da Pergunta</label>
                        <input type="text" v-model="questionText" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="correctAnswer" class="form-label">Resposta Correta</label>
                        <select v-model="correctAnswer" class="form-select" required>
                            <option :value="true">Verdadeiro</option>
                            <option :value="false">Falso</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="questionImage" class="form-label">Imagem (Opcional)</label>
                        <input type="file" @change="handleImageUpload" class="form-control" />
                    </div>

                    <button type="submit" class="btn btn-success">Criar Pergunta</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '@/components/NavDashboard.vue';
export default {
    name: 'QuestionCreateView',
    components: {
        NavDashboard,
    },
    data() {
        return {
            quizzes: [], // Lista de quizzes
            selectedQuizId: null, // ID do quiz selecionado
            questionText: '', // Texto da pergunta a ser criada
            correctAnswer: true, // Resposta correta (verdadeiro ou falso)
            questionImage: null, // Imagem da pergunta
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

        // Função para lidar com o upload da imagem
        handleImageUpload(event) {
            this.questionImage = event.target.files[0];
        },

        // Função para criar a pergunta
        async createQuestion() {
            if (!this.selectedQuizId) {
                alert('Selecione um quiz antes de criar a pergunta!');
                return;
            }

            try {
                const token = localStorage.getItem('authToken');
                const formData = new FormData();
                
                formData.append('text', this.questionText);
                formData.append('correct_answer', this.correctAnswer ? 1 : 0);

                if (this.questionImage) {
                    formData.append('image', this.questionImage); // Adiciona a imagem, se selecionada
                }

                await axios.post(`/api/quizzes/${this.selectedQuizId}/questions`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });

                alert('Pergunta criada com sucesso!');
                this.selectedQuizId = null;
                this.questionText = '';
                this.correctAnswer = true;
                this.questionImage = null;
            } catch (error) {
                console.error('Erro ao criar pergunta:', error);
                alert('Erro ao criar a pergunta. Tente novamente.');
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
