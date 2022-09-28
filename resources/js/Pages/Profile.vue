<script setup>
	import MatchSummaryCard from '../Components/MatchSummaryCard.vue';
	import Navbar from '../Components/Navbar.vue';
	import { ref } from 'vue';
	const matchData1 = {
		p1: {
			username: 'david.j123',
			eloDiff: 64,
			elo: '2500',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/27.jpg',
			finalScore: 11,
		},
		p2: {
			username: 'pianoman90',
			eloDiff: -29,
			elo: '1243',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/11.jpg',
			finalScore: 10
		}
	};

	const matchData2 = {
		p1: {
			username: 'david.j123',
			eloDiff: -143,
			elo: '2436',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/27.jpg',
			finalScore: 1,
		},
		p2: {
			username: 'bshizzlexx',
			eloDiff: 198,
			elo: '1775',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/0.jpg',
			finalScore: 11
		}
	};

	const matchData3 = {
		p1: {
			username: 'david.j123',
			eloDiff: 89,
			elo: '2579',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/27.jpg',
			finalScore: 11,
		},
		p2: {
			username: 'fir3start3r',
			eloDiff: -76,
			elo: '2105',
			avatarUrl: 'https://xsgames.co/randomusers/assets/avatars/male/54.jpg',
			finalScore: 6
		}
	};
	const match1 = ref(matchData1);
	const match2 = ref(matchData2);
	const match3 = ref(matchData3);
	
	const bg = ref('bg-emerald-700');
	const text = ref('text-white');
	const textMuted = ref('text-emerald-100');
</script>

<template>
	<Navbar />
	<body class="flex flex-col items-center" :class="bg">
		<div class="header flex items-center mt-4">
			<img class="profile-pic rounded-full w-44 shadow-2xl" src="https://xsgames.co/randomusers/assets/avatars/male/27.jpg">
			<div class="username ml-4 font-semibold text-2xl" :class="text">david.j123</div>
		</div>
		<div class="stats flex justify-around w-3/6 mt-8">
			<div class="stat-container flex flex-col items-center justify-center text-center">
				<div class="stat-data font-semibold text-2xl" :class="text">#3</div>
				<div class="stat-label" :class="textMuted">Current Ranking</div>
			</div>
			<div class="stat-container flex flex-col items-center justify-center text-center">
				<div class="stat-data font-semibold text-2xl" :class="text">2500</div>
				<div class="stat-label" :class="textMuted">Current Elo</div>
			</div>
			<div class="stat-container flex flex-col items-center text-center">
				<div class="stat-data font-semibold text-2xl" :class="text">123</div>
				<div class="stat-label" :class="textMuted">Matches Played</div>
			</div>
			<div class="stat-container flex flex-col items-center text-center">
				<div class="stat-data font-semibold text-2xl" :class="text">76%</div>
				<div class="stat-label" :class="textMuted">Win Rate</div>
			</div>
		</div>
		<div class="recent-matches w-3/6 flex flex-col items-center mt-6">
			<div class="flex justify-between w-full">
				<div class="text-lg ml-2" :class="textMuted">Recent Matches</div>
			</div>
			<MatchSummaryCard :playerOne="match1.p1" :playerTwo="match1.p2" />
			<MatchSummaryCard :playerOne="match2.p1" :playerTwo="match2.p2" />
			<MatchSummaryCard :playerOne="match3.p1" :playerTwo="match3.p2" />
			<div class="flex justify-end w-full">
				<div class="text-sm hover:underline cursor-pointer mt-1 mr-2" :class="text">View Complete Match History</div>
			</div>
		</div>
	</body>
</template>
