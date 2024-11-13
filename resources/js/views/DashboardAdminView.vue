<template>
    <div class="container my-5">
        <NavDashboard />
        <!-- Restante do conteúdo do Dashboard -->
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">Bem-vindo, {{ adminName }}</h3>
                <p>Aqui você pode gerenciar quizzes.</p>
            </div>
            <div class="row m-4">
                <div class="col-12">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Quizzes Cadastrados</h5>
                            <p class="card-text">{{ quizzes.length }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavDashboard from '@/components/NavDashboard.vue';
import axios from 'axios';

export default {
    name: 'DashboardAdminView',
    components: {
        NavDashboard,
    },
    data() {
        return {
            adminName: 'Admin User',
            quizzes: [], // Lista de quizzes
        };
    },
    methods: {
        async fetchQuizzes() {
            try {
                const token = localStorage.getItem('authToken');
                
                // Carregar quizzes
                const quizzesResponse = await axios.get('/api/quizzes', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.quizzes = quizzesResponse.data; // Atualiza a lista de quizzes
            } catch (error) {
                console.error('Erro ao carregar quizzes:', error);
            }
        },
    },
    mounted() {
        this.fetchQuizzes(); // Carrega quizzes ao montar a view
    },
};
</script>

<style scoped>
</style>
