<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-users mr-3"> </i>Users 
                </span>

                <span class="text-xl mr-2 font-bold float-right cursor-pointer"
                    @click="newUser = true"
                >
                    <i class="fa-sharp fa-solid fa-user-plus"></i>
                </span>
            </div>

            <div class="w-full flex flex-row p-2 mt-5">
                <div class="w-2/12">
                    <Toggle :value="options.maintenance.is_active" :url="'/users/system-maintenance'" :id="options.maintenance.id" class="pt-1"/> <span class="text-lg font-semibold">System Maintenance</span>
                </div>

                <div class="w-2/12">
                    <button class="w-full bg-red-500 py-2 text-white" style="border-radius: 30px" @click="notify()">
                        Notify Maintenance
                    </button>
                </div>
            </div>

            <div class="w-full h-full mt-5 flex flex-row">
                <div :style="{width: newUser ? '80%' : '100%'}" class="mx-2">
                    <Table :columns="columns" :rows="users" :keys="keys" :selected.sync="user"/> 
                </div>

                <div style="width: 20%;" 
                    class="mx-2 h-full" v-if="newUser && !selected"
                >   
                    <div class="h-auto flex flex-col"
                        style="border: 1px solid black; border-radius: 10px"
                    >
                        <div class="w-full">
                            <span class="ml-2 mt-2 cursor-pointer"
                                @click="newUser = false"
                            > 
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="text" class="w-full  my-2 --login__register--input text-center"
                                placeholder="First Name" v-model="formRegisterData.first_name"
                            >
                            <span class="text-xs text-red-500">{{validationError('first_name', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="text" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Middle Name" v-model="formRegisterData.middle_name"
                            >
                            <span class="text-xs text-red-500">{{validationError('middle_name', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="text" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Last Name" v-model="formRegisterData.last_name"
                            >
                            <span class="text-xs text-red-500">{{validationError('last_name', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="text" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Contact" v-model="formRegisterData.phone"
                            >
                            <span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="text" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Email" v-model="formRegisterData.email"
                            >
                            <span class="text-xs text-red-500">{{validationError('email', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <select v-model="formRegisterData.user_type" class="w-full text-center my-2 --login__register--input">
                                <option value="staff">Staff</option>
                                <option value="reader">Reader</option>
                                <option value="utility">Utility</option>
                            </select>

                            <span class="text-xs text-red-500">{{validationError('user_type', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="password" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Password" v-model="formRegisterData.password"
                            >
                            <span class="text-xs text-red-500">{{validationError('password', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <input type="password" class="w-full  my-2 --login__register--input text-center"
                                placeholder="Confirm Password" v-model="formRegisterData.confirm_password"
                            >
                            <span class="text-xs text-red-500">{{validationError('confirm_password', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <button class="w-full --login__register--button text-center"
                                @click="createAccount()"
                            >
                                Submit
                            </button>
                        </div>
                    </div>
                </div>

                <div style="width: 20%;" 
                    class="mx-2 h-full" v-if="selected"
                >   
                    <div class="h-auto flex flex-col"
                        style="border: 1px solid black; border-radius: 10px"
                    >   
                        <div class="w-full p-2 text-xl">
                            <span class="font-bold"
                            > 
                                {{ selected.first_name }} {{ selected.last_name }}
                            </span>

                            <span class=" cursor-pointer float-right"
                                @click="user = null"
                            > 
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>
                        

                        <div class="w-full flex flex-row p-2 mt-5">
                            <div class="w-2/12">
                                <Toggle :value="selected.is_active" :url="'/users/deactivate-reactivate'" :id="selected.id" class="pt-1"/> 
                            </div>
                            
                            <div class="w-10/12 px-2">    
                                <span class="text-lg font-semibold">{{ selected.is_active ? 'Deactivate'  : 'Reactivate' }}</span>
                            </div>
                        </div>


                        <div class="w-full flex flex-col p-2">
                            <div class="w-full">
                                <button class="bg-red-500 w-full text-white" style="height: 40px; border-radius: 5px" @click="resetPassword()">
                                    RESET PASSWORD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import Table from "../Components/Table";
import Toggle from '../Components/Toggle.vue';
import axios from "axios";

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table,
        Toggle
    },

    data() {
        return {
            columns: [
                'Name', 'Email', 'Contact', 'User Type', 'Account #', 'Status'
            ],
            keys : [
                {
                    label: 'name',
                },
                {
                    label: 'email',
                },
                {
                    label: 'phone',
                },
                {
                    label: 'user_type',
                },
                {
                    label: 'reference',
                },
                {
                    label: 'status',
                },
            ],
            users: [],
            user: null,
            newUser: false,
            formRegisterData: {
				first_name: null,
				middle_name: null,
				last_name: null,
				phone: null,
				email: null,
				password: null,
				confirm_password: null,
				role: 1,
				user_type: 'staff',
			},
            saveError: null,
            selected: null
        }
    },

    watch: {
        user(arg) {
            if(!arg) {
                this.selected = null;
                return;
            }

            this.selected = Object.assign({}, arg);
        }
    },

    mounted() {
        this.users = this.options.users
        console.log(this.options)
    },

    methods: {
        createAccount() {
			axios.post(this.$root.route + "/users/create-account", this.formRegisterData)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						this.formRegisterData = {
							first_name: null,
                            middle_name: null,
                            last_name: null,
							phone: null,
							email: null,
							password: null,
							confirm_password: null,
							role: 1,
							user_type: 'staff',
						}

						alert("Account successfully created.");

						this.saveError = null

                        location.reload()
					}
				})
		},

        resetPassword(){
            axios.post(this.$root.route + "/users/reset-password", this.selected)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
                        location.reload()
					}
				})
        },

        notify(){
            axios.post(this.$root.route + "/users/notify-maintenance", {})
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
                        alert('Notify all client successfully')
                        
                        location.reload()
					}
				})
        }
    }
}

</script>

<style scoped>
.--login__register--input {
	height: 40px;
	border-radius: 10px;
    border: 1px solid black;
}

.--login__register--button {
	height: 40px;
	border-radius: 30px;
	background: #2C3333;
	color: white 
}

.userModal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.user-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px; 
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

</style>
