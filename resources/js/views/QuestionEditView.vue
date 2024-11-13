<template>
    <div class="container my-5">
        <div class="card">
            <div class="card-body">
                <h5>Editar Pergunta</h5>
                
                <!-- Selecione o quiz para editar as perguntas -->
                <div class="mb-3">
                    <label for="quizSelect" class="form-label">Selecione um Quiz</label>
                    <select v-model="selectedQuizId" @change="fetchQuestions" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option v-for="quiz in quizzes" :key="quiz.id" :value="quiz.id">{{ quiz.title }}</option>
                    </select>
                </div>

                <!-- Selecione a pergunta a ser editada -->
                <div v-if="selectedQuizId">
                    <div class="mb-3">
                        <label for="questionSelect" class="form-label">Selecione uma Pergunta</label>
                        <select v-model="selectedQuestionId" @change="loadQuestionData" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option v-for="question in questions" :key="question.id" :value="question.id">{{ question.text }}</option>
                        </select>
                    </div>
                </div>

                <!-- Formulário de edição da pergunta -->
                <div v-if="selectedQuestionId">
                    <form @submit.prevent="updateQuestion">
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

                        <button type="submit" class="btn btn-warning">Atualizar Pergunta</button>
                    </form>
                    
                    <!-- Botão para excluir a pergunta -->
                    <button @click="deleteQuestion" class="btn btn-danger mt-2">Excluir Pergunta</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';

export default {
    name: 'QuestionEditView',
    data() {
        return {
            quizzes: [], // Lista de quizzes
            selectedQuizId: null, // ID do quiz selecionado
            selectedQuestionId: null, // ID da pergunta selecionada
            questions: [], // Lista de perguntas do quiz
            questionText: '', // Texto da pergunta a ser editada
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

        // Função para carregar as perguntas do quiz selecionado
        async fetchQuestions() {
            if (!this.selectedQuizId) return;

            try {
                const token = localStorage.getItem('authToken');
                const response = await axios.get(`/api/quizzes/${this.selectedQuizId}/questions`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.questions = response.data;
            } catch (error) {
                console.error('Erro ao carregar perguntas:', error);
            }
        },

        // Função para carregar os dados da pergunta selecionada
        async loadQuestionData() {
            if (!this.selectedQuizId || !this.selectedQuestionId) return;

            try {
                const token = localStorage.getItem('authToken');
                // Ajuste do endpoint para pegar a pergunta de um quiz específico
                const response = await axios.get(`/api/quizzes/${this.selectedQuizId}/questions/${this.selectedQuestionId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                // Preenche os campos com os dados da pergunta selecionada
                this.questionText = response.data.text;
                this.correctAnswer = response.data.correct_answer === 1; // Resposta correta: 1 para verdadeiro, 0 para falso
                this.questionImage = null; // Reseta a imagem
            } catch (error) {
                console.error('Erro ao carregar a pergunta:', error);
            }
        },

        // Função para lidar com o upload da imagem
        handleImageUpload(event) {
            this.questionImage = event.target.files[0];
        },

        // Função para atualizar a pergunta
        async updateQuestion() {
            try {
                const token = localStorage.getItem('authToken');
                const formData = new FormData();
                
                formData.append('_method', 'PUT'); // Para Laravel interpretar como atualização
                formData.append('text', this.questionText);
                formData.append('correct_answer', this.correctAnswer ? 1 : 0);

                if (this.questionImage) {
                    formData.append('image', this.questionImage); // Adiciona a imagem, se selecionada
                }

                await axios.post(`/api/questions/${this.selectedQuestionId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });

                alert('Pergunta atualizada com sucesso!');
                this.selectedQuizId = null;
                this.selectedQuestionId = null;
                this.questionText = '';
                this.correctAnswer = true;
                this.questionImage = null;
            } catch (error) {
                console.error('Erro ao atualizar pergunta:', error);
                alert('Erro ao atualizar a pergunta. Tente novamente.');
            }
        },
        // Função para excluir a pergunta
        async deleteQuestion() {
            if (confirm('Você tem certeza que deseja excluir esta pergunta?')) {
                try {
                    const token = localStorage.getItem('authToken');
                     const response = await axios.delete(`/api/questions/${this.selectedQuestionId}`, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });

                    alert('Pergunta excluída com sucesso!');
                    this.selectedQuizId = null;
                    this.selectedQuestionId = null;
                    this.questionText = '';
                    this.correctAnswer = true;
                    this.questionImage = null;

                    // Atualiza a lista de perguntas após exclusão
                    this.fetchQuestions();
                } catch (error) {
                    console.error('Erro ao excluir a pergunta:', error);
                    alert('Erro ao excluir a pergunta. Tente novamente.');
                }
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
