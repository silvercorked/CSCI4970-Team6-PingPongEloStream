<script setup>
import { ref } from 'vue';
const navItems = ref(['Play A Match', 'Leaderboard', 'About', 'Profile']);
const bg = ref('bg-emerald-600');
const fg = ref('text-white');

</script>

<template>
<nav class="navbar flex items-center" :class="bg">
	<div class="logo flex items-center text-2xl font-bold" :class="[fg, bg]">
		<font-awesome-icon icon="fa-solid fa-table-tennis-paddle-ball" class="mx-4 my-2 w-10 h-10" :class="fg"/>
		Attic Ping Pong
	</div>
	<div class="nav-item mx-12 text-lg" :class="fg" v-for="(navItem, index) in navItems" :key="index">
		<a href="/">{{ navItem }}</a>
	</div>
</nav>
</template>