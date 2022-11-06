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

            <div class="w-full h-full mt-5 flex flex-row">

                <div :style="{width: newUser ? '80%' : '100%'}" class="mx-2">
                    <Table :columns="columns" :rows="users" :keys="keys" :selected.sync="user"/> 
                </div>

                <div style="width: 20%;" 
                    class="mx-2 h-full" v-if="newUser"
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
                'Name', 'Email', 'Contact', 'User Type', 'Line #'
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
            saveError: null
        }
    },

    watch: {
        user(arg) {
        }
    },

    mounted() {
        this.users = this.options.users
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

</style>
