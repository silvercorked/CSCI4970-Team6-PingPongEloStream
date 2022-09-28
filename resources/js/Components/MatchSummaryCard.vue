<template>
	<div class="match-summary-card rounded-sm flex justify-between items-center shadow-lg bg-white rounded-xl px-4 my-1 border-none border-black-400 hover:bg-gray-100 transition-colors w-full min-w-[550px]">
		<!-- Left player -->
		<div class="player-left flex items-center ml-1">
			<!-- Profile pic and username -->
			<div class="name-pic-container flex flex-col items-center my-2">
				<img :src="playerOne.avatarUrl" class="w-12 rounded-full">
				<div class="username font-semibold text-black">{{ playerOne.username }}</div>
			</div>
			<!-- Elo diff and resulting elo -->
			<div class="elo-container flex items-center">
				<div v-if="playerOne.eloDiff > 0" class="elo-gained text-green-500 mx-3 font-semibold text-sm">
					+{{ playerOne.eloDiff }}
				</div>
				<div v-if="playerOne.eloDiff < 0" class="elo-lost text-red-500 mx-3 font-semibold text-sm">
					{{ playerOne.eloDiff }}
				</div>
				<div class="elo text-xl">
					{{ playerOne.elo }}
				</div>
			</div>
		</div>


		<!-- Final match scores -->
		<div class="score font-bold text-3xl flex items-center">
			<div class="left-score" :class="playerOne.finalScore > playerTwo.finalScore ? 'text-green-500' : 'text-red-500'">{{ playerOne.finalScore }}</div>
			<div class="dash text-2xl mx-2">-</div>
			<div class="right-score" :class="playerTwo.finalScore > playerOne.finalScore ? 'text-green-500' : 'text-red-500'">{{ playerTwo.finalScore }}</div>
		</div>

		<!-- Right player -->
		<div class="player-right flex items-center mr-1">
			<!-- Elo diff and resulting elo -->
			<div class="elo-container flex items-center">
				<div class="elo text-xl">
					{{ playerTwo.elo }}
				</div>
				<div v-if="playerTwo.eloDiff > 0" class="elo-gained text-green-500 mx-3 font-semibold text-sm">
					+{{ playerTwo.eloDiff }}
				</div>
				<div v-if="playerTwo.eloDiff < 0" class="elo-lost text-red-500 mx-3 font-semibold text-sm">
					{{ playerTwo.eloDiff }}
				</div>
			</div>
			<!-- Profile pic and username -->
			<div class="name-pic-container flex flex-col items-center my-2">
				<img :src="playerTwo.avatarUrl" class="w-12 rounded-full">
				<div class="username font-semibold">{{ playerTwo.username }}</div>
			</div>
		</div>
	</div>
</template>

<script>
	import { ref } from 'vue';
	export default {
		props: {
			playerOne: Object,
			playerTwo: Object,
		},
	}
</script>