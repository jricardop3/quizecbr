<template>
    <div class="container my-5">
        <div class="row">
            <!-- Coluna principal com o Quiz -->
            <div class="col-md-8">
                <h1 class="text-center">Responda o Quiz </h1>

                <div v-if="questions.length && !quizCompleted" class="question-card mt-5" v-for="(question, index) in questions" :key="question.id">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title text-center">Pergunta {{ index + 1 }}</h5>

                            <!-- Exibe a imagem da pergunta, se disponível -->
                            <div v-if="question.image" class="text-center mb-3">
                                <img :src="`/storage/${question.image}`" alt="Imagem da Pergunta" class="img-fluid card-img-top " style="width: 100%; height: 40vh;"/>
                            </div>

                            <p class="card-text">{{ question.text }}</p>

                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    :name="`answer-${question.id}`"
                                    value="true"
                                    v-model="answers[question.id]"
                                />
                                <label class="form-check-label">Verdadeiro</label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    :name="`answer-${question.id}`"
                                    value="false"
                                    v-model="answers[question.id]"
                                />
                                <label class="form-check-label">Falso</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="successMessage" class="alert alert-success text-center">{{ successMessage }}</div>
                <div v-if="errorMessage" class="alert alert-danger text-center">{{ errorMessage }}</div>
                <button v-if="questions.length && !quizCompleted" class="btn btn-primary mt-3" @click="submitQuiz">Concluir Quiz</button>

                <div v-else-if="quizCompleted" class="text-center">
                    <p>Quiz completo! Obrigado por participar.</p>
                    <p>Sua pontuação: {{ score }}</p>
                    <router-link to="/quiz" class="btn btn-primary mt-3">Ir para Quiz novamente</router-link>
                </div>
            </div>

            <!-- Coluna com o Ranking do Quiz -->
            <div class="col-md-4 text-center">
                <router-link to="/quiz" class="btn btn-primary">Voltar aos Quizzes</router-link>
                <h2 class="text-center">Top 10 do Quiz</h2>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome</th>
                            <th>Pontuação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in quizRanking" :key="user.id">
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
    name: 'AnswerQuizView',
    props: ['id', 'userId'],
    data() {
        return {
            successMessage: null,
            errorMessage: null,
            questions: [],
            answers: {}, // Armazena as respostas do usuário
            score: 0,
            quizCompleted: false,
            quizRanking: [], // Ranking específico para o quiz
        };
    },
    methods: {
        async fetchQuestions() {
            try {
                const token = localStorage.getItem('authToken');
                const response = await axios.get(`/api/quizzes/${this.id}/questions`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                this.questions = response.data;
                console.log('Perguntas carregadas:', this.questions);
            } catch (error) {
                console.error('Erro ao carregar perguntas:', error);
                this.errorMessage = 'Erro ao carregar perguntas. Verifique o console para mais detalhes.';
            }
        },
        async fetchQuizRanking() {
            try {
                const token = localStorage.getItem('authToken');
                const response = await axios.get(`/api/ranking/quiz/${this.id}`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                this.quizRanking = response.data;
                console.log('Ranking do quiz carregado:', this.quizRanking);
            } catch (error) {
                console.error('Erro ao carregar o ranking do quiz:', error);
            }
        },
        async submitQuiz() {
            try {
                const token = localStorage.getItem('authToken');
                const formattedAnswers = Object.keys(this.answers).map(questionId => ({
                    question_id: parseInt(questionId),
                    answer: this.answers[questionId] === 'true',
                }));

                const response = await axios.post(`/api/quizzes/${this.id}/participations`, {
                    answers: formattedAnswers,
                }, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.successMessage = 'Participação registrada com sucesso!';
                this.score = response.data.user_score.score;
                this.quizCompleted = true;
                console.log('Participação registrada:', response.data);
            } catch (error) {
                console.error('Erro ao registrar participação:', error);
                this.errorMessage = 'Usuário já participou deste quiz tente outro por favor.';
            }
        },
    },
    async mounted() {
        console.log('Montando componente e carregando perguntas...');
        await this.fetchQuestions();
        await this.fetchQuizRanking(); // Carrega o ranking específico do quiz ao montar o componente
    },
};
</script>

<style scoped>
.question-card {
    max-width: 600px;
    margin: 0 auto;
}
</style>
