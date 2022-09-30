<script setup>
  defineProps({
    user: Object,
  });
</script>



<template>
  <h1>Profile show</h1>
  <div>User: {{ user }}</div>
</template>