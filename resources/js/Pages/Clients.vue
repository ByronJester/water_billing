<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-users mr-3"> </i>Clients
                </span>
            </div>

            <div class="w-full h-full mt-5 flex flex-row">

                <div style="width: 80%" class="mx-2">
                    <Table :columns="columns" :rows="clients" :keys="keys" :selected.sync="client"/>
                </div>

                <div style="width: 20%" class="mx-2 flex flex-col" v-if="!!client">
                    <div class="py-2 px-3 flex flex-col"
                        style="width:100%; border: 1px solid black; border-radius: 5px"
                    >   
                        <div class="w-full">
                            <span class="ml-2 cursor-pointer"
                                @click="client = null"
                            >  
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full text-center">
                            <span class="text-xl font-bold">
                                {{ client.name }}
                            </span>
                        </div>

                        <div class="w-full mt-8 pl-2">
                            <p class="text-xl">
                                Line #: <span class="ml-2"> {{ client.reference }} </span>
                            </p>
                        </div>

                        <div class="w-full mt-4 flex flex-row pl-2">
                            <div class="w-2/12">
                                <Toggle :value="client.is_active" :url="'/clients/deactivate-reactivate'" :id="client.id" class="pt-1"/> 
                            </div>
                            
                            <div class="w-10/12">    
                                <span class="text-lg font-semibold">{{ client.is_active ? 'Disconnect'  : 'Reconnect' }}</span>
                            </div>
                        </div>

                        <div class="w-full mt-4 inline-flex justify-center">
                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #0288D1"
                                @click="viewClient(client.reference)" class="mr-1"
                            >
                                VIEW
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #00897B" 
                                @click="markAsPaid(client.id)" class="mr-1" v-if="client.amount_to_pay > 0"
                            >
                                MARK AS PAID
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #EF5350"
                                @click="notify(client)" v-if="client.amount_to_pay > 0"
                            >
                                NOTIFY
                            </button>


                        </div>
                    </div>

                </div>

                <div style="width: 20%" class="mx-2 flex flex-col" v-else>
                    <div class="pb-3"
                        style="width:100%;"
                    >
                        <span class="text-xl">
                            <i class="fa-solid fa-square-plus mr-2"></i>New Connection
                        </span>
                        
                    </div>

                    <div class="py-5 px-3"
                        style="width:100%; border: 1px solid black; border-radius: 5px"
                    >
                        <div>
                            <label for="name">First Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.first_name">
                            <span class="text-xs text-red-500">{{validationError('first_name', saveError)}} </span>
                        </div>

                        <div>
                            <label for="name">Middle Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.middle_name">
                            <span class="text-xs text-red-500">{{validationError('middle_name', saveError)}} </span>
                        </div>

                        <div>
                            <label for="name">Last Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.last_name">
                            <span class="text-xs text-red-500">{{validationError('last_name', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Address:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.address">
                            <span class="text-xs text-red-500">{{validationError('address', saveError)}} </span>
                        </div>

                        <div class="mt-6">
                            <button class="--btn py-2" @click="createClient()">
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
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import Toggle from '../Components/Toggle.vue';

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table,
        Toggle
    },
    data(){
        return {
            columns: [
                'Name', 'Address', 'Amount To Pay', 'Penalty', 'Line #', 'Due Date'
            ],
            keys : [
                {
                    label: 'name',
                },
                {
                    label: 'address',
                },
                {
                    label: 'amount_to_pay',
                },
                {
                    label: 'penalty',
                },
                {
                    label: 'reference',
                },
                {
                    label: 'due_date',
                },
            ],
            clients: [],
            form: {
                first_name: null,
                middle_name: null,
                last_name: null,
                address: null
            },
            saveError: null,
            client: null
        }
    },

    mounted() {
        this.clients = this.options.clients
    },

    watch: {
        client(arg) {
            
        }
    },

    methods: {
        createClient(){
            let headers = {
				'Access-Control-Allow-Origin': '*',
				'Access-Control-Allow-Methods': 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS',
				'Access-Control-Allow-Headers': 'Content-Type'
			}
			
			axios.post(this.$root.route + "/clients/client/create", this.form)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						this.form = {
							name: null,
							address: null
						}

						alert("New connection successfully created.");

						this.saveError = null

						location.reload()
					}
				})
        },

        viewClient(arg) {
            Inertia.get(
                this.$root.route + '/clients/' + arg,
                {
                    onSuccess: () => { },
                },
            );
        },

        markAsPaid(client_id) {
            axios.post(this.$root.route + "/clients/client/mark-as-paid", {id : client_id})
				.then(response => {
                    alert("Succesfully mark as paid.");
					location.reload()
				})
        },

        notify(arg) {
            axios.post(this.$root.route + "/clients/client/notify", {reference : arg.reference, due_date: arg.due_date})
				.then(response => {
                    alert("Client has successfuly notified of his/her payment due date.");
					location.reload()
				})
        }
    }
}

</script>

<style scoped>
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