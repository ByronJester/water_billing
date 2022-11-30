<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full h-full mt-5 flex flex-row">

                <div class="mx-2" :style="{'width': activeTab == 'clients' ? '80%' : '100%'}">
                    <div class="w-full flex flex-row mb-3 font-bold" style="height: 50px; border-bottom: 1px solid black">
                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'dashboard'" :class="{'bg-blue-300': activeTab == 'dashboard' }">
                            DASHBOARD
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'clients'" :class="{'bg-blue-300': activeTab == 'clients' }">
                            CLIENTS
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'billing'" :class="{'bg-blue-300': activeTab == 'billing' }">
                            BILLING
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'cashiering'" :class="{'bg-blue-300': activeTab == 'cashiering' }">
                            CASHIERING
                        </div>
                    </div>

                    <div class="w-full flex flex-col" v-if="activeTab != 'dashboard'">
                        <div class="w-full">
                            <input type="text" style="height: 50px; border: 1px solid black; border-radius: 10px; width: 300px" class="px-5 float-right"
                                placeholder="Search..." v-model="search"
                            >
                        </div>

                        <div class="w-full mt-5">
                            <Table :columns="columns" :rows="clients" :keys="keys" :selected.sync="client" class="w-full"/>
                        </div>
                        
                    </div>
                    
                    <div class="w-full flex flex-row justify-center items-center mt-5" v-else>
                        <div class="w-full flex flex-col mx-2 cursor-pointer" style="border: 1px solid black; border-radius: 5px" @click="activeTab = 'clients'">
                            <div class="w-full text-center" style="font-size: 50px;">
                                {{ options.clients.length}}
                            </div>

                            <div class="w-full text-center">
                                Clients
                            </div>
                        </div>

                        <div class="w-full flex flex-col mx-2 cursor-pointer" style="border: 1px solid black; border-radius: 5px" @click="changeActive('/users')">
                            <div class="w-full text-center" style="font-size: 50px;">
                                {{ options.users.length}}
                            </div>

                            <div class="w-full text-center">
                                Users
                            </div>
                        </div>

                        <div class="w-full flex flex-col mx-2 cursor-pointer" style="border: 1px solid black; border-radius: 5px" @click="changeActive('/reports')">
                            <div class="w-full text-center text-red-600" style="font-size: 50px;">
                                {{ options.incidents.length}}
                            </div>

                            <div class="w-full text-center">
                                Incident Reports
                            </div>
                        </div>
                    </div>
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
                                Account #: <span class="ml-2"> {{ client.reference }} </span>
                            </p>
                        </div>

                        <div class="w-full mt-4 flex flex-row pl-2" v-if="activeTab == 'clients'">
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
                                v-if="activeTab == 'billing'"
                            >
                                VIEW
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #00897B" 
                                @click="markAsPaid(client.id)" class="mr-1" v-if="client.amount_to_pay > 0 && activeTab == 'cashiering'"
                            >
                                MARK AS PAID
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #EF5350"
                                @click="notify(client)" v-if="client.amount_to_pay > 0 && activeTab == 'cashiering'"
                            >
                                NOTIFY
                            </button>


                        </div>
                    </div>

                </div>

                <div style="width: 20%" class="mx-2 flex flex-col" v-if="activeTab == 'clients' && !client">
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
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.first_name" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('first_name', saveError)}} </span>
                        </div>

                        <div>
                            <label for="name">Middle Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.middle_name" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('middle_name', saveError)}} </span>
                        </div>

                        <div>
                            <label for="name">Last Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.last_name">
                            <span class="text-xs text-red-500">{{validationError('last_name', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Address:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.address" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('address', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Contact #:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.phone">
                            <span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>
                        </div>

                        <div class="mt-6">
                            <button class="--btn py-2" @click="createClient()">
                                Submit
                            </button>
                        </div>
                        
                    </div>
                </div>   
            </div>

            <VueHtml2pdf
                :show-layout="false"
                :float-layout="true"
                :enable-download="true"
                :preview-modal="true"
                :paginate-elements-by-height="2000"
                :filename="Math.random().toString(36).slice(2)"
                :pdf-quality="2"
                :manual-pagination="false"
                pdf-format="a4"
                pdf-orientation="landscape"
                pdf-content-width="100%"
                @hasDownloaded="hasDownloaded($event)"
                ref="or"
            >
                <section slot="pdf-content">
                    <div class="flex flex-col p-4 w-full h-screen">
                        <div class="w-full flex flex-col" v-if="!!client">
                            <div class="w-full text-center text-xl pt-4 pb-10" style="border-bottom: dashed black;">
                                WATER BILLING MANAGEMENT SYSTEM
                            </div>

                            <div class="w-full text-center font-bold text-lg my-5">
                                OFFICIAL WATER RECEIPT
                            </div>


                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Account No.
                                </span>

                                <span class="float-right mr-5">
                                    {{client.reference }}
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Name
                                </span>

                                <span class="float-right mr-5">
                                    {{client.name }}
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Due Date
                                </span>

                                <span class="float-right mr-5">
                                    {{client.due_date }}
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Consumption
                                </span>

                                <span class="float-right mr-5">
                                    {{client.cubic_meter_consumed }} Cubic Meter
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Service Charge
                                </span>

                                <span class="float-right mr-5">
                                    {{ parseFloat(client.other_fee).toFixed(2) }}
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Penalty
                                </span>

                                <span class="float-right mr-5">
                                    {{ parseFloat(client.penalty).toFixed(2) }}
                                </span>
                            </div>

                            <div class="w-full">
                                <span class="float-left ml-5">
                                    Water Bill
                                </span>

                                <span class="float-right mr-5">
                                    {{ parseFloat(client.amount_to_pay).toFixed(2) }}
                                </span>
                            </div>

                            <div class="w-full py-10 my-5" style="border-bottom: dashed black; border-top: dashed black">
                                <span class="float-left ml-5">
                                    Total Bill
                                </span>

                                <span class="float-right mr-5">
                                    {{ parseFloat(client.total).toFixed(2) }}
                                </span>
                            </div>

                            <div class="w-full">
                                <p>
                                    You may check your bill online @ water-billing-6mb6.onrender.com <br>
                                    For any inquiries, please contact <br>
                                    09566814383/09657657443 <br>
                                </p>
                            </div>

   
                        </div>
                    </div>
                </section>
            </VueHtml2pdf>

        </div>
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import Table from "../Components/Table";
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import Toggle from '../Components/Toggle.vue';
import VueHtml2pdf from 'vue-html2pdf';

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table,
        Toggle,
        VueHtml2pdf
    },
    data(){
        return {
            columns: [
                'Name', 'Address', 'Account #'
            ],
            keys : [
                {
                    label: 'name',
                },
                {
                    label: 'address',
                },
                {
                    label: 'reference',
                }
            ],
            clients: [],
            form: {
                first_name: null,
                middle_name: null,
                last_name: null,
                address: null,
                phone: null
            },
            saveError: null,
            client: null,
            activeTab: 'dashboard',
            search: null
        }
    },

    mounted() {
        this.clients = this.options.clients
    },

    watch: {
        search(arg) {
            this.clients = this.options.clients.filter(x => {
                var name = x.name.toLowerCase();
                var search = arg.toLowerCase()
                return name.includes(search)
            });
        },

        client(arg) {
            
        },
        activeTab(arg){
            if(arg == 'clients') {
                this.columns = [
                    'Name', 'Address', 'Contact #', 'Account #'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'address',
                    },
                     {
                        label: 'phone',
                    },
                    {
                        label: 'reference',
                    }
                ]
            }

            if(arg == 'billing') {
                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Penalty', 'Other Fees', 'Due Date'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'amount_to_pay',
                    },
                    {
                        label: 'penalty',
                    },
                    {
                        label: 'other_fee',
                    },
                    {
                        label: 'due_date'
                    }
                ]
            }

            if(arg == 'cashiering') {
                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Penalty', 'Other Fees', 'Total'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'amount_to_pay',
                    },
                    {
                        label: 'penalty',
                    },
                    {
                        label: 'other_fee',
                    },
                    {
                        label: 'total',
                    },
                ]
            }
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
                            first_name: null,
                            middle_name: null,
                            last_name: null,
                            address: null,
                            phone: null
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
					// location.reload()

                    this.$refs.or.generatePdf()
				})
        },

        notify(arg) {
            axios.post(this.$root.route + "/clients/client/notify", {reference : arg.reference, due_date: arg.due_date})
				.then(response => {
                    alert("Client has successfuly notified of his/her payment due date.");
					// location.reload()
				})
        },
        changeActive(arg){
            Inertia.get(
                this.$root.route + arg,
                {
                    onSuccess: () => { },
                },
            );
        },

        hasDownloaded(evt) {
            location.reload()
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