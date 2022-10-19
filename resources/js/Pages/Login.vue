<template>
	<div class="w-screen h-screen flex flex-row">
		<div style="background: #B0BEC5" :style="{ width: mobile ? '100%' : '20%'}"
			class="flex justify-center items-center"
			v-if="!!isLogin || !!mobile"
		>
			<div v-if="!isRegister"
				class="mx-2" style="border: 1px solid black; border-radius: 5px;"
			>	
				<div class="w-full" v-if="!mobile">
					<span class="float-right mr-3 cursor-pointer"
						@click="isLogin = false"
					> 
						<i class="fa-sharp fa-solid fa-xmark"></i>
					</span>
				</div>

				<div class="px-2 py-2">
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

					<p class="text-black text-sm ml-14 cursor-pointer hover:underline"
						@click="isRegister = true"
						v-if="!mobile"
					>
						Doesn't have accout ? Sign Up Here!
					</p>
				</div>
			</div>

			<div class="mx-2" style="border: 1px solid black; border-radius: 5px;" v-else>
				<div class="w-full">
					<span class="float-right mr-3 cursor-pointer"
						@click="isLogin = false"
					> 
						<i class="fa-sharp fa-solid fa-xmark"></i>
					</span>
				</div>

				<div class="px-2 py-2">
					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Name" v-model="formRegisterData.name"
					>
					<span class="text-xs text-red-500">{{validationError('name', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Email" v-model="formRegisterData.email"
					>
					<span class="text-xs text-red-500">{{validationError('email', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Contact" v-model="formRegisterData.phone"
					>
					<span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>

					<input type="text" class="w-full  my-2 --login__register--input text-center"
						placeholder="Reference" v-model="formRegisterData.reference"
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

					<p class="text-black text-sm ml-14 cursor-pointer hover:underline"
						@click="isRegister = false"
					>
						Already have account ? Login Here!
					</p>
				</div>
			</div>
		</div>

		<div :style="{ width: !isLogin ? '100%' : '80%'}" v-if="!mobile" class="relative">
			<div class="w-full mt-10 flex flex-row"
				style="padding-left: 12rem; padding-right: 12rem;"
			>
				<div class="w-full text-center cursor-pointer py-4"
					style="background: #2596be"
					:class="{'font-bold': active == 'home'}"
					@click="changeActive('home')"
				>
					HOME	
				</div>

				<div class="w-full text-center cursor-pointer py-4"
					style="background: #2596be"
					:class="{'font-bold': active == 'client'}"
					@click="changeActive('client')"
				>
					REQUIREMENTS
				</div>

				<div class="w-full text-center cursor-pointer py-4"
					style="background: #2596be"
					:class="{'font-bold': active == 'about'}"
					@click="changeActive('about')"
				>
					ABOUT US
				</div>

				<div class="w-full text-center cursor-pointer py-4"
					style="background: #2596be"
					:class="{'font-bold': active == 'contact'}"
					@click="changeActive('contact')"
				>
					CONTACT US
				</div>

				<div class="w-full text-center cursor-pointer py-4"
					style="background: #2596be"
					@click="isLogin = true;"
				>
					LOGIN
				</div>
			</div>

			<div class="w-full flex justify-center items-center"
			>
				<div class="w-4/5 --banner"
					style="height: 250px;"
				>
					
				</div>
			</div>

			<div class="w-full flex justify-center items-center">
				<div class="w-4/5 h-full"
				>
					<div class="w-full flex flex-row" v-if="active == 'client'">
						<div class="w-full">
							<p class="text-xl font-bold mb-5 mt-5 text-center">
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
							<p class="text-xl font-bold mb-5 mt-5 text-center">
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
							<p class="text-xl font-bold mb-5 mt-5 text-center">
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

					<div class="w-full mt-8 flex justify-center items-center" v-if="active == 'about'">
						<div class="w-6/12 flex flex-col" style="">
							<div class="w-full mt-2">
								<p class="text-xl font-bold">
									About Us
								</p>

								<p class="text-lg font-semibold text-justify mt-4">
									Water Billing System is a website that helps consumer to monitor their water usage, also consumers can see future announcemnt about the water billing.
								</p>

								<p class="text-xl font-bold mt-8">
									Mission
								</p>

								<p class="text-lg font-semibold text-justify mt-4">
									There's a perception that you have to be brutal to grow a business management. But we are aware that there is a better method to develop one in which what's good for the client’s satisfactory and also benefits the bottom line. We think that companies can develop a conscience and thrive with a soul. Because technology is being used more effectively, we made the decision to come up with a better way to handle water services, simplify client payment processes, and streamline the work of water readers.
								</p>

								<p class="text-xl font-bold mt-8">
									Vision
								</p>

								<p class="text-lg font-semibold text-justify mt-4">
									The approach we developed will enable us to prioritize client’s satisfaction while speeding up workers productivity.
								</p>
							</div>
						</div>
					</div>

					<div class="w-full" v-if="active == 'contact'">
						Contact
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
	props:['message'],
	data(){
		return {
			formloginData : {
				email: null,
				password: null 
			},
			formRegisterData: {
				name: null,
				phone: null,
				email: null,
				password: null,
				confirm_password: null,
				role: 2,
				user_type: 'client',
				reference: null
			},
			isRegister: false,
			saveError: null,
			mobile: window.screen.width <= 400,
			isLogin: null,
			active: 'home'
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
			Inertia.post(this.$root.route + "/users/login", this.formloginData,
			{
				onSuccess: (res) => {
				},
				orError: (err) => {
				}
			});
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
						this.formRegisterData = {
							name: null,
							phone: null,
							email: null,
							password: null,
							confirm_password: null,
							role: 2,
							user_type: 'client',
							reference: null
						}

						alert("Account successfully created.");

						this.saveError = null

						this.isRegister = false
					}
				})
		},
		changeActive(arg){
			this.active = arg
		}
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
	background: #2C3333;
	color: white
}


</style>