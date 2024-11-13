<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary mx-auto">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <router-link to="/admin-dashboard"  class="nav-link">
                    <button class="nav-link btn btn-success">Dashboard</button>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to="/criar-quiz"  class="nav-link">
                    <button class="nav-link btn btn-success">Criar Quiz</button>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to="/editar-quiz"  class="nav-link">
                    <button class="nav-link btn btn-warning">Editar Quiz</button>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to="/criar-pergunta"  class="nav-link">
                    <button class="nav-link btn btn-success">Criar Pergunta</button>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link to="/editar-pergunta" class="nav-link">
                    <button class="nav-link btn btn-warning">Editar Pergunta</button>
                </router-link>
            </li>
        </ul>
    </nav>
</template>
<script>
export default {
    name: 'NavDashboard',
};
</script>