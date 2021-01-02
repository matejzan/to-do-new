<template>
	<div class="navbar">		
		<div class="login">
			<button class="logout" v-if="loggedUser" @click="logOutUser">Log out</button>
		</div>
	</div>
</template>


<style scoped>
	
	.navbar p {
		color: #269d44;
		margin: 0px;
		font-size: 18px;
	}

	.navbar {
		position: absolute;
		top: 0px;
		background: none;
		display: flex;
		align-items: center;
		height: 0px;
		width: 100%; 
	}

	.login {
		display: inline-flex;
		width: 50%;
		align-items: center;
		justify-content: flex-start;
	}

	.logout {
		background: none;
		width: 100px;
		height: 30px;
		margin-left: 20px;
		border: 2px solid #269d44;
		border-radius: 15px;
		color: #269d44;
		font-size: 16px;
		cursor: pointer;
		transition: 0.1s ease-in-out;
        -webkit-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
	}

	.logout:hover {
		background: #269d44;
		color: white;
	}

	@media (max-width: 766px) {
		.container70 {
			width: 90%;
			margin: 0px 5%;
		}

		.logo {
			width: 30%;
		}

		.login {
			width: 70%;
		}

		.navbar p {
			white-space: nowrap;
		}
	}

</style>

<script>

export default {
	data: function() {
		return {
			loggedUser: null
		}
	},

	mounted: function() {
        this.loggedUser = document.querySelector('meta[name="loggedUser"]').content;
    },

    methods: {
    	logOutUser: function() {
    		console.log("logout");
    		axios.post('logout').then(function (response) {
    			if (response.status == 200) {
    				location.reload();
    			}
    		});
    	}
    }
};

</script>