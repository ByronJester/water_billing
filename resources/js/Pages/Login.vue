<template>
	<div class="w-screen h-screen --main">

		<div class="w-full flex flex-col">
			<div class="w-full flex flex-row" style="height: 10vh; background: navy">
				<div :style="{width: mobile ? '15%': '7%'}" class="flex justify-center items-center pl-1">
					<img src="images/logo.png" class="w-full" style="height: 8vh">
				</div>

				<div :style="{width: mobile ? '85%': '93%'}" class="flex flex-row items-center px-2 text-white" :class="{'--xxs': mobile}">
					<div class="w-full inline-flex" :class="{'justify-start': mobile, 'justify-end': !mobile,}">
						<span class="mx-1 cursor-pointer">
							<a href="#home">HOME</a>
						</span>

						<span class="mx-1 cursor-pointer">
							<a href="#requirements">REQUIREMENTS</a>
						</span>

						<span class="mx-1 cursor-pointer">
							<a href="#aboutus">ABOUT US</a>
						</span>

						<span class="mx-1 cursor-pointer">
							<a href="#contactus">CONTACT US</a>
						</span>
					</div>
				</div>

			</div>

			<div id="registerModal" class="registerModal">
				<!-- Modal content -->
				<div class="register-content flex flex-col" style="border: 2px solid navy" :style="{'width' : mobile ? '80%' : '30%'}">
					<div class="w-full">
						<span class="text-sm font-bold">
							VERIFICATION CODE WAS SENT TO CONTACT NO. OF ACCOUNT #:{{ formRegisterData.reference }}
						</span>

						<!-- <span class="float-right cursor-pointer"
							@click="closeRegisterModal()"
						>
							<i class="fa-solid fa-xmark"></i>
						</span> -->
					</div>

					<div class="w-full flex flex-col mt-4">
						<div class="w-full">
							<input type="text" class="w-full  my-2 --login__register--input text-center" style="border: 1px solid black"
								placeholder="Verification Code" v-model="formRegisterData.code" 
							>
							<span class="text-xs text-red-500">{{validationError('code', saveError)}} </span><br>
						</div>
					</div>

					<div class="w-full flex flex-col mt-4">
						<button class="w-full --login__register--button" @click="register()">
							Proceed
						</button>
					</div>
				</div>
			</div>

			<div class="w-screen h-screen --home flex justify-center items-center" id="home">

				<div class="p-2" style="border: 2px solid black; border-radius: 5px; width: 500px" v-if="!isRegister">
					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Email" v-model="formloginData.email"
						@keyup.enter="login()"
					>

					<input type="password" class="w-full mt-2 --login__register--input text-center"
						:class="{'mb-2' : !message}"
						placeholder="Password" v-model="formloginData.password"
						@keyup.enter="login()"
					>

					<span class="text-red-500 text-xs ml-2" v-if="message">
						{{ message }}
					</span>

					<button class="w-full  my-2 --login__register--button text-center"
						@click="login()"
						:disabled="disableButton()"
						:class="{ 'cursor-not-allowed' : disableButton() }"
					>
						Login
					</button>

					<p class="text-white text-sm cursor-pointer hover:underline text-center"
						@click="isRegister = true"
					>
						Doesn't have account ? Sign Up Here! 
					</p>
				</div>

				<div class="p-2" style="border: 2px solid black; border-radius: 5px; width: 500px" v-else>
					<input type="text" class="w-full  my-2 --login__register--input text-center" style="text-transform: capitalize;"
						placeholder="First Name" v-model="formRegisterData.first_name"
					>
					<span class="text-xs text-red-500">{{validationError('first_name', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center" style="text-transform: capitalize;"
						placeholder="Middle Name" v-model="formRegisterData.middle_name"
					>
					<span class="text-xs text-red-500">{{validationError('middle_name', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center" style="text-transform: capitalize;"
						placeholder="Last Name" v-model="formRegisterData.last_name"
					>
					<span class="text-xs text-red-500">{{validationError('last_name', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Email" v-model="formRegisterData.email"
					>
					<span class="text-xs text-red-500">{{validationError('email', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Contact" v-model="formRegisterData.phone"
					>
					<span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Account #" v-model="formRegisterData.reference"
					>
					<span class="text-xs text-red-500">{{validationError('reference', saveError)}} </span>

					<input type="password" class="w-full mt-2 --login__register--input text-center"
						:class="{'mb-2' : !message}"
						placeholder="Password" v-model="formRegisterData.password"
					>
					<span class="text-xs text-red-500">{{validationError('password', saveError)}} </span>

					<input type="password" class="w-full mt-2 --login__register--input text-center"
						:class="{'mb-2' : !message}"
						placeholder="Confirm Password" v-model="formRegisterData.confirm_password"
					>
					<span class="text-xs text-red-500">{{validationError('confirm_password', saveError)}} </span>

					<button class="w-full  my-2 --login__register--button text-center"
						@click="register()"
					>
						Register
					</button>

					<p class="text-white text-sm text-center cursor-pointer hover:underline"
						@click="isRegister = false"
					>
						Already have account ? Login Here!
					</p>
				</div>
			</div>

			<div class="w-screen h-full --requirements flex justify-center items-center" id="requirements">
				<div class="flex flex-col md:flex-row" style="width: 80%">
					<div class="w-full">
						<p class="text-xl font-bold mb-5 mt-5">
							APPLICATION FOR NEW CONNECTION
						</p>

						<p class="mb-3">
							<b>Schedule of Availability of Service</b><br>
							Monday to Friday<br>
							8:00 A.M. to 5:00 P.M.<br>
						</p>

						<p class="mb-5">
							<b>Who may avail of the service?</b><br>
							Applicants covered by the service areas.<br><br>

							<b>What is the requirement?</b><br>
							- Company I.D. <br>
							- Current School I.D.<br>
							- Voter’s I.D.<br>
							- Driver’s License<br>
							- Updated Postal I.D.<br>
							- Passport<br>
							- One (1) copy any of 1×1/2×2 ID Picture<br>
							- Photocopy of Brgy. Clearance<br><br>

							<b>How to avail of the service:</b><br>
							1.Visit the office;<br>
							2. Tell your purpose;<br>
							3. Wait to be entertained by the staff;<br>
							4. Staff to advise the applicant for schedule of inspection and field survey;<br>
							5. Staff to get the complete address and contact number of the applicant;<br>
							6. The applicant must be sure to get list of requirements.<br>

						</p>
					</div>

					<div class="w-full">
						<p class="text-xl font-bold mb-5 mt-5">
							APPLICATION FOR RECONNECTION
						</p>

						<p class="mb-3">
							<b>Schedule of Availability of Service</b><br>
							Monday to Friday<br>
							8:00 A.M. to 5:00 P.M.<br>
						</p>

						<p class="mb-5">
							<b>Who may avail of the service?</b><br>
							Those connections who were disconnected.<br><br>

							<b>What is the requirement?</b><br>
							Full payment of unpaid water bills, re-connection fee and other charges if any<br><br>

							<b>How to avail of the service:</b><br>
							1. Visit the office;<br>
							2. Tell your purpose;<br>
							3. Wait to be entertained by the in-charge;<br>
							4. Submit your residence certificate;<br>
							5. Staff to verify record of accounts and status of connection;<br>
							6. Signing of Service Application and Construction Order and cost estimates;<br>
							7. Forward to the Admin for approval;<br>
							8. Payment to cashier and wait for the issuance of Official Receipt;<br>
							9. Signing of Application and Agreement for Water Service Connection;<br>
							10. Staff will indicate schedule of installation.<br>
						</p>
					</div>

					<div class="w-full">
						<p class="text-xl font-bold mb-5 mt-5">
							APPLICATION FOR DISCONNECTION
						</p>

						<p class="mb-3">
							<b>Schedule of Availability of Service</b><br>
							Monday to Friday<br>
							8:00 A.M. to 5:00 P.M.<br>
						</p>

						<p class="mb-5">
							<b>Who may avail of the service?</b><br>
							Those who wish to temporarily disconnect their water service installed through
							pull-out water meter due to any valid reason that service needs to be disconnected.
							<br><br>

							<b>What is the requirement?</b><br>
							Request for disconnection of service line.<br>
							Full payment of outstanding water bills and materials under Promissory Note if any<br><br>

							<b>How to avail of the service:</b><br>
							1.Proceeds to customer service and requests disconnection<br>
							2.Signs Disconnection Logbook<br>
							3. Pays all unpaid water bills and other charges under installment scheme (if any)<br>
							4. Accepts work done, signs Maintenance Order<br>
						</p>
					</div>
				</div>
			</div>

			<div class="w-screen h-full --aboutus flex justify-center items-center text-center mt-10 flex flex-col" id="aboutus">
				<div class="w-full">
					<p class="text-center w-full">
						<span class="text-4xl">
							ABOUT US
						</span><br><br>

						<span class="text-lg">
							Water Billing System is a website that helps consumer to monitor their water usage,<br> 
							also consumers can see future announcement about the water billing.
						</span>
					</p>
				</div>

				<div class="w-full">
					<p class="text-center mt-10 w-full">
						<span class="text-4xl">
							VISION
						</span><br><br>

						<span class="text-lg">
							There's a perception that you have to be brutal to grow a business management.<br>
							But we are aware that there is a better method to develop one in which what's <br>
							good for the client’s satisfactory and also benefits the bottom line. <br>
							We think that companies can develop a conscience and thrive with a soul. <br>
							Technology is being used more effectively, we made the decision to come 
							up with a better way to handle water services, simplify client payment <br>
							processes, and streamline the work of water readers.
						</span>
					</p>
				</div>

				<div class="w-full">
					<p class="text-center mt-10 w-full">
						<span class="text-4xl">
							MISSION
						</span><br><br>

						<span class="text-lg">
							The approach we developed will enable us to prioritize client’s <br>
							satisfaction while speeding up workers productivity
						</span>
					</p>
				</div>
			</div>

			<div class="w-screen h-full --contactus text-center mt-10 flex flex-col justify-center items-center text-xs md:text-2xl" id="contactus"
				style="background: navy; color: white !important; " :style="{'height': mobile ? '100%' : '20vh'}"
			>
				<div style="width: 100%" class="flex flex-col md:flex-row">
					<div class="w-full mt-5 md:mt-0">
						<i class="fa-solid fa-envelope"></i><p>waterbillingsystem1@gmail.com</p>
					</div>

					<div class="w-full mt-5 md:mt-0">
						<i class="fa-brands fa-facebook"></i><p>Water Billing System</p>
					</div>

					<div class="w-full mt-5 md:mt-0">
						<i class="fa-sharp fa-solid fa-phone"></i><p>0991-702-1532/0965-765-7443</p>
					</div>
				</div>

				<div style="width: 100%" class="flex flex-col md:flex-row mt-5 md:mt-10">
					<div class="w-full text-center">
						<i class="fa-regular fa-copyright"></i><p>All Rights Reserve 2022</p>
					</div>
				</div>
			</div>

		</div>
		


	</div>

</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";

export default {
	props:[],
	data(){
		return {
			formloginData : {
				email: null,
				password: null 
			},
			formRegisterData: {
				first_name: null,
				middle_name: null,
				last_name: null,
				phone: null,
				email: null,
				password: null,
				confirm_password: null,
				role: 2,
				user_type: 'client',
				reference: null,
				code: null
			},
			isRegister: false,
			saveError: null,
			mobile: window.screen.width <= 400,
			isLogin: false,
			active: 'home',
			message: null
		}
	},

	watch: {
		isRegister: function(arg) {
			this.saveError = null
			this.message = null
		}
	},

	mounted(){
		
	},

	methods: {
		login() {
			// Inertia.post(this.$root.route + "/users/login", this.formloginData,
			// {
			// 	onSuccess: (res) => {
			// 	},
			// 	orError: (err) => {
			// 	}
			// });

			axios.post(this.$root.route + "/users/login", this.formloginData)
				.then(response => {
					if(response.data.status == 422) {
						this.message = response.data.message
					} else {
						location.reload()
					}
				})
		},

		disableButton(){
			if(!this.formloginData.email){
				return true;
			}

			if(!this.formloginData.password){
				return true;
			}

			return false;
		},

		register() {
			axios.post(this.$root.route + "/users/create-client-account", this.formRegisterData)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						
						if(this.formRegisterData.code == null) {
							this.openRegisterModal()
						} else {
							alert("Account successfully created.");

							location.reload()
						}
					}
				})
		},
		changeActive(arg){
			this.active = arg
		},

		openRegisterModal(){
            var modal = document.getElementById("registerModal");
            modal.style.display = "block";
        },
        closeRegisterModal(){
            var modal = document.getElementById("registerModal");
            modal.style.display = "none";
        },
	}
}

</script>

<style scoped>
.--panel {
	background-image: url('/images/bg.jpg');
	background-repeat: no-repeat;
 	background-size: 100vw 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
}

.--banner {
	background-image: url('/images/banner.png');
	background-repeat: no-repeat;
 	background-size: 100% 100%;
}

.--login__register {
	width: 380px;
	height: auto;
	background: #607EAA;
	border-radius: 10px;
	position: relative;
}

.--login__register--input {
	height: 40px;
	border-radius: 10px;
}

.--login__register--button {
	height: 40px;
	border-radius: 30px;
	background: navy;
	color: white
}

.--xxs {
	font-size: 11px;
	color: white;
}

.--home {
	background-image: linear-gradient(rgba(4,9,30,0.7),rgba(4,9,30,0.7)), url('/images/meter1.png');
	background-repeat: no-repeat;
 	background-size: 100vw 100vh;
}

.registerModal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 40%;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
}
/* Modal Content */
.register-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

</style>
