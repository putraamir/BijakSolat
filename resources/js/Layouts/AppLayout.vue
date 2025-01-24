<!-- resources/js/Layouts/AppLayout.vue -->
<template>
    <div>
      <Sidebar
        :is-authenticated="$page.props.auth.user !== null"
        :user-name="$page.props.auth.user?.name"
        :user-role="$page.props.auth.user?.role"
        :user-avatar="$page.props.auth.user?.avatar"
      >
        <slot></slot>
      </Sidebar>
      <Register />
      <Login />

    </div>
  </template>

  <script>
  import Sidebar from '@/Components/Sidebar.vue';
  import Register from '@/Pages/Auth/Register.vue';
  import Login from '@/Pages/Auth/Login.vue';

  export default {
    components: {
      Sidebar,
    },
  };
  </script>
