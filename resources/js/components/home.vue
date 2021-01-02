<template>
	<div class="homepage-wrapper">

		<div v-if="gotaccalready" class="login-wrapper">

            <h1>Log in and get those tasks done!</h1>

	    	<form action="javascript:void(0)" class="login">
	    		<input type="hidden" :value="csrfToken" name="_token"/>

	            <input name="mail" v-model="mailLogin" type="text" placeholder="Email address" id="E-mail">
	            <p>{{ mailLoginError }}</p>

	            <input name="password" v-model="passwordLogin" type="password" placeholder="Password">
	            <p>{{ passwordLoginError }}</p>

	            <button @click="checkLoginInput"><p>Login!</p></button>
	            <p>{{ loginResponse }}</p>
	            <img v-if="loadingLogin" class="loader" src="assets/images/loader.gif">

	        </form>

            <h2>No account yet?<b @click="gotaccalready = false"> Register!</b></h2>
    	</div>

		<div v-else class="register-wrapper">

            <h1>Register and make your life easier!</h1>

	    	<form action="javascript:void(0)" class="register">
	    		<input type="hidden" :value="csrfToken" name="_token"/>

	            <input name="name" v-model="name" type="text" placeholder="Name">
	            <p>{{ nameError }}</p>

	            <input name="surname" v-model="surname" type="text" placeholder="Surname">
	            <p>{{ surnameError }}</p>

	            <input name="mail" v-model="mail" type="text" placeholder="Email address" id="E-mail">
	            <p>{{ mailError }}</p>

	            <input name="password" v-model="password" type="password" placeholder="Password">
	            <p>{{ passwordError }}</p>

	            <input name="registerPasswordRepeat" v-model="passwordRepeat" type="password" placeholder="Repeat password">
	            <p>{{ passwordRepeatError }}</p>

	            <button @click="checkInput"><p>Register!</p></button>
	            <p>{{ registrationResponse }}</p>
	            <img v-if="loadingRegister" class="loader" src="assets/images/loader.gif">

	        </form>

            <h2>Already got an account?<b @click="gotaccalready = true"> Log in!</b></h2>
    	</div>   	

	</div>
</template>

<script>

export default {
    data: function () {
        return {
          name: '',
          surname: '',
          mail: '',
          password: '',
          passwordRepeat: '',
          nameError: '',
          surnameError: '',
          mailError: '',
          passwordError: '',
          passwordRepeatError: '',
          registrationResponse: '',
          mailLogin: '',
          passwordLogin: '',
          mailLoginError: '',
          passwordLoginError: '',
          loginResponse: '',
          loadingLogin: false,
          loadingRegister: false,
          csrfToken: null,
          gotaccalready: true
        }        
    },
    mounted: function() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    },
    methods: {

        checkInput: function() {
            var RegisterForm = this;
            var submitable = false;

            RegisterForm.registrationResponse = "";
            
            var validName = this.checkNameValidity(this.name);
            this.nameError = validName.error;
            
            var validSurname = this.checkSurnameValidity(this.surname);
            this.surnameError = validSurname.error;

            var validEmail = this.checkEmailValue(this.mail); //CHECK MAIL FORMAT
            this.mailError = validEmail.error;

            var validPassword = this.checkPasswordValidity(this.password); //CHECK PASS LEN - min 8 characters
            this.passwordError = validPassword.error;

            var validPasswordRepeat = this.checkPasswordMatching(this.password, this.passwordRepeat); //CHECK PASS MATCHING
            this.passwordRepeatError = validPasswordRepeat.error;

            if (validName.valid && validSurname.valid && validEmail.valid && validPassword.valid && validPasswordRepeat.valid) {
                submitable = true;
            }

            if (submitable) {
            	RegisterForm.loadingRegister = true;
                axios.post('postregister', { //POST REQUEST TO STORE TASK TO DB
				    'name': this.name,
		            'surname': this.surname,
		            'mail': this.mail,
		            'password': this.password,
		            'registerPasswordRepeat': this.passwordRepeat
				}).then(function(response) { //GET RESPONSE
					if (response.status == 200) { //CHECK RESPONSE

						if(response.data == 1) { //RESPONSE FOR EXISTING USER
							RegisterForm.loadingRegister = false;
							RegisterForm.registrationResponse = "User already exists!"
						}
						else { //SUCCESSFULL REGISTRATION - RELOAD TO MY TASKS
							location.reload();
							RegisterForm.loadingRegister = false;
						}
					}
				});		
            }
        },

        checkLoginInput: function() {
            var LoginForm = this;
            var submitable = false;

            LoginForm.loginResponse = "";
            
            var validEmail = this.checkEmailValue(this.mailLogin); //CHECK MAIL FORMAT
            this.mailLoginError = validEmail.error;

            var validPassword = this.checkPasswordValidity(this.passwordLogin); //CHECK PASS LEN - min 8 characters
            this.passwordLoginError = validPassword.error;

            if (validEmail.valid && validPassword.valid) {
                submitable = true;
            }

            if (submitable) {
            	LoginForm.loadingLogin = true;
                axios.post('postlogin', { //POST REQUEST TO STORE TASK TO DB
		            'mail': this.mailLogin,
		            'password': this.passwordLogin
				}).then(function(response) { //GET RESPONSE	
					if (response.status == 200) { //CHECK RESPONSE

						if(response.data == 2) { //RESPONSE FOR EXISTING USER
							LoginForm.loadingLogin = false;
							LoginForm.loginResponse = "User doesn't exist!";
						}
						else if(response.data == 1) { //RESPONSE FOR EXISTING USER
							LoginForm.loadingLogin = false;
							LoginForm.loginResponse = "Incorrect password!";
						}
						else { //SUCCESSFULL REGISTRATION - RELOAD TO MY TASKS
							location.reload();
							LoginForm.loadingLogin = false;
						}
					}
				});		
            }
        },
        
        checkNameValidity: function(name) {
            if(!name.length) {
                return { valid: false, error: "Name field is required"};
            }
            else {
                return { valid: true, error: null};
            }
        },

        checkSurnameValidity: function(surname) {
            if(!surname.length) {
                return { valid: false, error: "Surname field is required"};
            }
            else {
                return { valid: true, error: null};
            }
        },

        checkEmailValue: function(mail) {
            if (!mail.length) {
                return { valid: false, error: "Email field is required" };
            }
            else if (!mail.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-]/)) {
                return { valid: false, error: "Invalid mail format" };
            }
            else if (mail.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-]/)) {
                return { valid: true, error:""};
            }
        },

        checkPasswordValidity: function(password) {
            if (!password.length) {
                return { valid: false, error: "Password field is required"};
            }
            else if (password.length < 8) {
                return { valid: false, error: "Password is too short"};
            }
            else {
                return { valid: true, error: null};
            }
        },

        checkPasswordMatching: function(password, passwordRepeat) {
            if (!passwordRepeat.length) {
                return { valid: false, error: "Password repeat field is required"};
            }
            else if (password != passwordRepeat) {
                return { valid: false, error: "Passwords do not match"};
            }
            else if (password == passwordRepeat) {
                return { valid: true, error: null};
            }
        },
    }
};

</script>

<style scoped>
	
	.homepage-wrapper {
        min-height: 100vh;
        padding:30px 0px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
        background: linear-gradient(60deg, #143f60, #53967e);
    }

    .login-wrapper {
        width: 400px;
    }

    .homepage-wrapper h1 {
        font-size: 28px;
        font-weight: 700;
        color: white;
        text-align: center;
    }

    .homepage-wrapper h2 {
        font-size: 20px;
        font-weight: 500;
        color: white;
        text-align: center;
        margin: 0px;
    }

    .homepage-wrapper b {
        font-size: 20px;
        font-weight: 700;
        color: white;
        cursor: pointer;
        transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
    }

    .homepage-wrapper b:hover {
        color: #cccccc;
    }

    .register-wrapper {
        width: 400px;
    }

    .register {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        padding: 15px 0px;
        box-sizing: border-box;
    }

    .login {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        padding: 15px 0px;
        box-sizing: border-box;
    }

    input {
        display: block;
        width: 400px;
        height: 50px;
        padding: 10px;
        margin: -5px 0px;
        background: rgba(0,0,0,0.4);
        color: white;
        font-size: 16px;
        box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2);
    }

    form p {
        color: red;
        font-size: 14px;
    }

    input:focus {
        outline: none;
    }

    ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: white;
        opacity: 1; /* Firefox */
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: white;
    }

    ::-ms-input-placeholder { /* Microsoft Edge */
        color: white;
    }

    button {
        background: rgba(0,0,0,0.4);
        width: 200px;
        height: 60px;
        border-radius: 15px;
        padding: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
        margin-bottom: 0px;
        cursor: pointer;
        transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
    }

    button p {
        color: white;
        font-size: 24px;
        transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
    }

    button:hover {
        background: rgba(0,0,0,0.9);
    }

    .loader {
    	height: 70px;
    	margin-top: -20px;
    }

    @media (max-width: 766px) {

        .homepage-wrapper {
            flex-direction: column;
        }

        .login-wrapper {
            width: 90%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .register-wrapper {
            width: 90%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
    }

    @media (max-width: 480px) {

        input {
            width: 100%;
            margin: -8px 0px;
        }

        .homepage-wrapper h1 {
            font-size: 24px;
        }

        .homepage-wrapper h2 {
            font-size: 18px;
        }

        .homepage-wrapper b {
            font-size: 18px;
        }
    }


</style>
