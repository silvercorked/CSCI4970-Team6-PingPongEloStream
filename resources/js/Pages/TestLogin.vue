<script setup>
import JetLabel from '@/Components/Label.vue';
</script>

<template>
	<JetLabel value="jet label" />
	<div class="body flex justify-center">
		<div class="login-container flex flex-col items-center w-[500px] bg-gray-100 rounded-3xl m-12 pb-5 border border-gray-300 shadow-lg">
			<p class="header text-4xl font-bold m-8">Login</p>
			<div class="input-container flex flex-col w-4/5 mb-6">
					Username	
					<input type="text" class="box-border rounded-md border border-black">
			</div>
			<div class="input-container flex flex-col w-4/5 mb-6">
					Password
					<input type="text" class="box-border rounded-md border border-black">
			</div>
			<button class="login-btn bg-black text-white rounded-lg text-lg transition-none w-56 h-16 hover:bg-gray-800 active:bg-gray-700">LOG IN</button>
			<div class="line w-4/5 mt-40 bg-opacity-10 bg-black h-[1px] mb-4"></div>
			<div class="footer">Don't have an account yet?
				<a href="#" class="font-bold hover:underline">Create one.</a>
			</div>
		</div>
	</div>
</template>
