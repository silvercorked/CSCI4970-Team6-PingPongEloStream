<script setup>

</script>

<template>
	<div class="body">
		<div class="login-container">
			<p class="header">Login</p>
			<div class="input-container">
					Username	
					<input type="text">
			</div>
			<div class="input-container">
					Password
					<input type="text">
			</div>
			<button class="login-btn">LOG IN</button>
			<div class="line"></div>
			<div class="footer">Don't have an account yet? <a href="#">Create one.</a></div>
		</div>
	</div>
</template>

<style scoped>
.body {
	background-color: #e6e6e6;
	height: 100vh;
	display: flex;
	justify-content: center;
}
.header {
	font-size: 40px;
	font-weight: bold;
	margin-bottom: 33px;
	margin-top: 45px;
}
.login-container {
	display: flex;
	background-color: white;
	flex-direction: column;
	width: 500px;
	border-radius: 25px;
	align-items: center;
	margin: 50px;
	height: 599px;
}
.input-container {
	display: flex;
	flex-direction: column;
	width: 80%;
	margin-bottom: 25px;
}
input[type=text] {
	border-radius: 8px;
	border: 1px solid #000000;
}
input[type=text]:active, input[type=text]:focus {
	border: 2px solid #000000;
	outline: none !important; 
	box-shadow: none;
}
.login-btn {
	background-color: #000000;
	color: white;
	border-radius: 10px;
	width: 223px;
	height: 64px;
	font-size: 18px;
	transition: 0.2s;
}
.login-btn:hover {
	background-color: hsl(0, 0%, 15%);
	transition: 0.2s;
}
.login-btn:active {
	background-color: hsl(0, 0%, 25%);
	transition: 0s;
}
.line {
	border: 1px solid #48734c;
	width: 100%;
	opacity: 25%;
	width: 423px;
	margin-top: 152px;
	height: 1px;
	margin-bottom: 16px;
}
a {
	color: black;
	font-weight: bold;
}
a:hover {
	color: black;
	transition: 0.2s;
	text-decoration: underline;
}
</style>