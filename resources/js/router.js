import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import LoginAdminView from '@/views/LoginAdminView.vue';
import LoginUserView from '@/views/LoginUserView.vue';
import RegisterUserView from '@/views/RegisterUserView.vue';
import QuizView from '@/views/QuizView.vue';
import AnswerQuizView from '@/views/AnswerQuizView.vue';


const routes = [
    {
        path: '/',
        name: 'Home',
        component: HomeView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/register-user');
            } else {
                next();
            }
        },
    },
    {
        path: '/login-admin',
        name: 'LoginAdmin',
        component: LoginAdminView,
    },
    {
        path: '/login-user',
        name: 'LoginUser',
        component: LoginUserView,
    },
    {
        path: '/register-user',
        name: 'RegisterUser',
        component: RegisterUserView,
    },
    {
        path: '/quiz',
        name: 'Quiz',
        component: QuizView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-user'); // Redireciona para o login se o usuário não estiver autenticado
            } else {
                next();
            }
        },
    },
    {
        path: '/quiz/:id',
        name: 'AnswerQuiz',
        component: AnswerQuizView,
        props: true, // Habilita o id como prop na view
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
