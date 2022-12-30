<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
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

            <div class="w-full flex flex-row mt-5 mb-3 font-bold" style="height: 50px; border-bottom: 1px solid black">
                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'user'" :class="{'bg-blue-300': activeTab == 'user' }"
                >
                    USER MAINTENANCE
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'cubic_meter'" :class="{'bg-blue-300': activeTab == 'cubic_meter' }"
                >
                    CUBIC METER MAINTENANCE
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'ir'" :class="{'bg-blue-300': activeTab == 'ir' }"
                >
                    SERVICES
                </div>
            </div>

            <div class="w-full h-full mt-5 flex flex-row" v-if="activeTab == 'cubic_meter'">
                <div style="width: 80%" class="mx-2">
                    <Table :columns="cColumns" :rows="options.bills" :keys="cKeys" :selected.sync="bill"/>
                </div>

                <div style="width: 20%" class="mx-2 flex flex-col">
                    <div class="pb-3"
                        style="width:100%;"
                    >
                        <span class="text-xl">
                            <i class="fa-solid fa-plus mr-2"></i>PER CUBIC METER 
                        </span>
                        
                    </div>

                    <div class="py-5 px-3"
                        style="width:100%; border: 1px solid black; border-radius: 5px"
                    >
                        <div>
                            <label for="name">Amount:</label><br>
                            <input type="text" class="--input py-4" v-model="form.amount">
                            <span class="text-xs text-red-500">{{validationError('amount', saveError)}} </span>
                        </div>

                        <div class="mt-6">
                            <button class="--btn py-2" @click="createBill()">
                                Submit
                            </button>
                        </div>
                        
                    </div>
                </div>
                
            </div>

            <div class="w-full h-full mt-5 flex flex-row" v-if="activeTab == 'user'">
                <div :style="{width: newUser ? '80%' : '100%'}" class="mx-2">
                    <div class="w-full my-2" v-if="activeTab == 'user'">
                        <label>User Type:</label>

                        <select v-model="user_type" class="text-center ml-2" style="width: 200px; border: 1px solid black; border-radius: 5px">
                            <option value="client">Client</option>
                            <option value="staff">Staff</option>
                            <option value="reader">Reader</option>
                            <option value="utility">Utility</option>
                            <option value="cashier">Cashier</option>
                        </select>

                        <span class="text-xl mr-2 font-bold cursor-pointer float-right"
                            @click="newUser = true"
                        >
                            <i class="fa-sharp fa-solid fa-user-plus"></i>
                        </span>
                    </div>

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
                                placeholder="Username" v-model="formRegisterData.username"
                            >
                            <span class="text-xs text-red-500">{{validationError('username', saveError)}} </span>
                        </div>

                        <div class="w-full px-4 p-2">
                            <select v-model="formRegisterData.user_type" class="w-full text-center my-2 --login__register--input">
                                <option value="staff">Staff</option>
                                <option value="reader">Reader</option>
                                <option value="utility">Utility</option>
                                <option value="cashier">Cashier</option>
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

            <div class="w-full h-full mt-5 flex flex-row" v-if="activeTab == 'ir'">
                <Table :columns="uColumns" :rows="utilities" :keys="uKeys" :selected.sync="utility"/>

                <div id="irModal" class="irModal">
                    <!-- Modal content -->
                    <div class="ir-content flex flex-col" style="width: 20%">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                            UTILITY ASSIGNMENT
                            </span>

                            <span class="float-right cursor-pointer"
                                @click="closeIrModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <select v-model="utilityForm.user_id" class="w-full --input"
                                style="height: 40px; padding-left: 10px; border: 1px solid black"
                            >
                                <option :value="worker.id" v-for="worker in options.workers" :key="worker.id">
                                    {{ worker.name }}
                                </option>
                            </select>
                        </div>



                        <div class="w-full mt-5">
                            <button class="w-full px-4 py-2 text-center" style="background: navy; color:white;"
                                @click="assignWorker()"
                            >
                                ASSIGN
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
                'Name', 'Username', 'Contact', 'User Type', 'Account #', 'Status'
            ],
            keys : [
                {
                    label: 'name',
                },
                {
                    label: 'username',
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
				username: null,
				password: null,
				confirm_password: null,
				role: 1,
				user_type: 'staff',
			},
            saveError: null,
            selected: null,
            activeTab: 'user',
            cColumns: [
                'Amount', 'Date', 'Personnel'
            ],
            cKeys : [
                {
                    label: 'amount',
                },
                {
                    label: 'date',
                },
                {
                    label: 'personnel',
                },
            ],
            form: {
                amount: 0
            },
            user_type: 'client',
            uColumns: [
                'Name', 'Address', 'Description', 'Worker', 'Status'
            ],
            uKeys : [
                {
                    label: 'client_name',
                },
                {
                    label: 'client_address'
                },
                {
                    label: 'description',
                },
                {
                    label: 'worker',
                },
                {
                    label: 'status',
                },
            ],
            utilities: [],
            utility: null,
            utilityForm: {
                user_id: null,
                id: null
            }
        }
    },

    watch: {
        user(arg) {
            if(!arg) {
                this.selected = null;
                return;
            }

            this.selected = Object.assign({}, arg);
        },
        user_type(arg) {
            this.users = this.options.users.filter( x => {
                return x.user_type == arg;
            })
        },
        utility(arg) {
            if(arg && arg.user_id == 1) {
                this.openIrModal(arg)
            }
            
        }
    },

    mounted() {
        this.users = this.options.users.filter( x => {
            return x.user_type == this.user_type;
        })

        this.utilities = this.options.utilities
    },

    methods: {
        openIrModal(arg){
            var modal = document.getElementById("irModal");
            modal.style.display = "block";

            this.utilityForm.id = arg.id
        },
        closeIrModal(){
            var modal = document.getElementById("irModal");
            modal.style.display = "none";

            this.utilityForm = {
                user_id: null,
                id: null
            }
        },
        assignWorker(){
            swal({
                title: "Are you sure to assign this utility worker?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/clients/client/assign-worker", this.utilityForm)
                        .then(response => {
                            if(response.data.status == 422) {
                                this.saveError = response.data.errors 
                            } else {
                                swal({
                                    title: "Utility Assignment",
                                    text: "You successfully assign utility worker.",
                                    icon: "success",
                                    button: "Okay",
                                }).then( (proceed)=> {
                                    this.saveError = null

                                    location.reload()
                                });
                            }
                        })
                }
            });
        },
        createAccount() {
            swal({
                title: "Are you sure to create this new user account ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
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
                                    username: null,
                                    password: null,
                                    confirm_password: null,
                                    role: 1,
                                    user_type: 'staff',
                                }

                                swal({
                                    title: "User's Account",
                                    text: "You successfully created new account.",
                                    icon: "success",
                                    button: "Okay",
                                }).then( (proceed)=> {
                                    this.saveError = null

                                    location.reload()
                                });
                            }
                        })
                }
            });

			
		},

        resetPassword(){
            swal({
                title: "Are you sure to reset password of this account ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/users/reset-password", this.selected)
                        .then(response => {
                            if(response.data.status == 422) {
                                this.saveError = response.data.errors 
                            } else {
                                swal({
                                    title: "Reset Password",
                                    text: "Successfully reset password of this account.",
                                    icon: "success",
                                    button: "Okay",
                                }).then( (proceed)=> {
                                    this.saveError = null

                                    location.reload()
                                });
                            }
                        })
                }
            });
            
        },

        notify(){

            swal({
                title: "Are you sure to notify clients ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/users/notify-maintenance", {})
                        .then(response => {
                            if(response.data.status == 422) {
                                this.saveError = response.data.errors 
                            } else {
                                swal({
                                    title: "Notify Clients",
                                    text: "Notify all client successfully",
                                    icon: "success",
                                    button: "Okay",
                                });
                            }
                        })
                }
            });
            
        },

        createBill(){
            swal({
                title: "Are you sure to update water bill per cubic meter ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/settings/create-bill", this.form)
                        .then(response => {
                            if(response.data.status == 422) {
                                this.saveError = response.data.errors 
                            } else {
                                this.form = {
                                    amount: null,
                                    date: null
                                }

                                swal({
                                    title: "Water Bill Per Cubic Meter",
                                    text: "Successfully added new water bill.",
                                    icon: "success",
                                    button: "Okay",
                                }).then( (proceed)=> {
                                    this.saveError = null

                                    location.reload()
                                });

                                
                            }
                        })
                }
            });
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

.irModal {
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
.ir-content {
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

.--input {
    width: 100%;
    height: 30px;
    border: 1px solid black;
    border-radius: 5px;
    text-align: center;
}

.--btn {
    background: #2B4865;
    border-radius: 10px;
    width: 100%;
    text-align: center;
    color: white;
}

</style>
