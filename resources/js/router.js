import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import LoginAdminView from '@/views/LoginAdminView.vue';
import LoginUserView from '@/views/LoginUserView.vue';
import RegisterUserView from '@/views/RegisterUserView.vue';
import QuizView from '@/views/QuizView.vue';
import AnswerQuizView from '@/views/AnswerQuizView.vue';
import DashboardAdminView from '@/views/DashboardAdminView.vue';
import QuizCreateView from '@/views/QuizCreateView.vue'; // Nova view para criar quiz
import QuizEditView from '@/views/QuizEditView.vue'; // Nova view para editar quiz
import QuestionCreateView from '@/views/QuestionCreateView.vue'; // Nova view para criar pergunta
import QuestionEditView from '@/views/QuestionEditView.vue'; // Nova view para editar pergunta

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
    {
        path: '/admin-dashboard',
        name: 'DashboardAdmin',
        component: DashboardAdminView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-admin');
            } else {
                next();
            }
        },
    },
    {
        path: '/criar-quiz',
        name: 'QuizCreate',
        component: QuizCreateView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-admin'); // Redireciona para o login se o administrador não estiver autenticado
            } else {
                next();
            }
        },
    },
    {
        path: '/editar-quiz',
        name: 'QuizEdit',
        component: QuizEditView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-admin'); // Redireciona para o login se o administrador não estiver autenticado
            } else {
                next();
            }
        },
    },
    {
        path: '/criar-pergunta',
        name: 'QuestionCreate',
        component: QuestionCreateView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-admin'); // Redireciona para o login se o administrador não estiver autenticado
            } else {
                next();
            }
        },
    },
    {
        path: '/editar-pergunta',
        name: 'QuestionEdit',
        component: QuestionEditView,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('authToken');
            if (!token) {
                next('/login-admin'); // Redireciona para o login se o administrador não estiver autenticado
            } else {
                next();
            }
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
