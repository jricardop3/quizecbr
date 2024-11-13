<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <ul class="navbar-nav">
        <li class="nav-item"><router-link to="/" class="nav-link">Home</router-link></li>
      </ul>
    </nav>
    <router-view />
  </template>
  
  <script>
  export default {
    name: 'App',
  };
  </script>
  
  <style scoped>

  </style>
  