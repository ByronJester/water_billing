<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full h-full mt-5 flex flex-row">

                <div class="mx-2" :style="{'width': activeTab == 'clients' ? '80%' : '100%'}">
                    <div class="w-full flex flex-row mb-3 font-bold" style="height: 50px; border-bottom: 1px solid black">
                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'dashboard'" :class="{'bg-blue-300': activeTab == 'dashboard' }"
                            v-if="auth.user_type != 'cashier'"
                        >
                            DASHBOARD
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'clients'" :class="{'bg-blue-300': activeTab == 'clients' }"
                            v-if="auth.user_type != 'cashier'"
                        >
                            CLIENTS
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'billing'" :class="{'bg-blue-300': activeTab == 'billing' }"
                            v-if="auth.user_type != 'cashier'"
                        >
                            BILLING
                        </div>

                        <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'cashiering'" :class="{'bg-blue-300': activeTab == 'cashiering' }" v-if="auth.user_type == 'cashier'">
                            CASHIERING
                        </div>
                    </div>

                    <div class="w-full flex flex-col" v-if="activeTab != 'dashboard' && activeTab != 'cashiering'">
                        <div class="w-full" v-if="!isViewPayment">
                            <input type="text" style="height: 50px; border: 1px solid black; border-radius: 10px; width: 300px" class="px-5 float-right"
                                placeholder="Search..." v-model="search"
                            >
                        </div>

                        <div class="w-full mt-5" v-if="!isViewPayment">
                            <Table :columns="columns" :rows="clients" :keys="keys" :selected.sync="client" class="w-full"/>
                        </div>

                        <div class="w-full mt-5" v-else>
                            <Table :columns="paymentColumns" :rows="payments" :keys="paymentKeys" :selected.sync="payment" class="w-full"/>
                        </div>
                        
                    </div>

                    <div class="w-full flex flex-col" v-else-if="activeTab == 'cashiering'">
                        <div class="w-full">
                            <!-- <label>
                                Client
                            </label><br> -->
                            <Dropdown
                                :options="options.activatedClients"
                                :disabled="false"
                                v-on:selected="selectClient"
                                name="connections"
                                :maxItem="5"
                                style="border: 1px solid black; width: 252px; float: left; border-radius: 3px"
                                placeholder="Please select client">
                            </Dropdown>
                        </div>

                        <div class="w-full flex flex-row mt-5" v-if="!!selectedClient">
                            <div style="width: 30%">
                                <div class="w-full text-2xl font-bold">
                                    Client's Personal Information
                                </div>
                                <div style="height: 500px; border: 1px solid black" class="w-full flex flex-col mt-2">
                                    <div class="w-full p-2">
                                        <label>
                                            First Name:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.first_name" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Middle Name:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.middle_name" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Last Name:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.last_name" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Address:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.address" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Contact #:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.phone" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Account #:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.reference" disabled>
                                    </div>

                                    <div class="w-full p-2">
                                        <label>
                                            Serial #:
                                        </label><br>
                                        <input type="text" class="--input" :value="selectedClient.serial" disabled>
                                    </div>

                                </div>
                            </div>

                            <div style="width: 70%" class="ml-5">
                                <div class="w-full text-2xl font-bold flex flex-row">
                                    <div class="w-full">
                                        <p> Billing Details </p> 
                                    </div>
                                    
                                    <div class="w-full" v-if="hasSelected">
                                        <select style="width: 90%; border: 1px solid black; border-radius: 5px; height: 34px; text-align: center" v-model="month" class="ml-1"> 
                                            <option :value="m.value" v-for="m in months.filter(x => { return availableMonths.includes(x.label) })" :key="m.value">
                                                {{ m.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-full" v-if="hasSelected">
                                        <input type="text" style="width: 90%; border: 1px solid black; border-radius: 5px; height: 34px; text-align: center" 
                                            class="ml-1"
                                            @keypress="validate(event)" v-model="paymentAmount"
                                        >
                                    </div>

                                    <div class="w-full" v-if="hasSelected">
                                        <button style="background: #000000; color: white; padding: 0px 5px 0px 5px; border-radius: 5px; background: #0288D1; width: 100%; font-size: 15px"
                                            @click="markAsPaid(selectedClient.id)" v-if="activeTab == 'cashiering'"
                                            class="ml-1"
                                        >
                                            SAVE PAYMENT
                                        </button>

                                        <span class="text-xs text-red-500" v-if="message">
                                            {{message}} 
                                        </span>
                                    </div>


                                </div>
                                <div style="height: 200px; border: 1px solid black" class="w-full flex flex-col items-center mt-2">
                                    <Table :columns="paymentColumns" :rows="payments.filter( x => { return x.status == 'UNPAID'})" :keys="paymentKeys" :selected.sync="payment" style="width: 98%" class="mt-2"/>
                                </div>
                                <div class="w-full">
                                    <span class="float-right text-2xl font-bold">
                                        Total: ₱ {{ parseFloat(unpaid_total).toFixed(2) }}
                                    </span>
                                </div>

                                <div class="w-full text-2xl font-bold mt-10">
                                    Payment History
                                </div>
                                
                                <div style="height: 250px; border: 1px solid black" class="w-full flex flex-col items-center mt-2">
                                    <Table :columns="otherPaymentColumns" :rows="histories" :keys="otherPaymentKeys" :selected.sync="payment" style="width: 98%" class="mt-2"/>
                                </div>
                            </div>
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

                        <div class="w-full flex flex-col mx-2 cursor-pointer" style="border: 1px solid black; border-radius: 5px" @click="changeActive('/users')">
                            <div class="w-full text-center text-red-600" style="font-size: 50px;">
                                {{ options.incidents.length}}
                            </div>

                            <div class="w-full text-center">
                                Services
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
                                @click="client = null; isViewPayment = false"
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

                        <div class="w-full mt-4 flex flex-row pl-2" v-if="activeTab == 'clients' && client.status == 'Activated'">
                            <div class="w-2/12">
                                <Toggle :value="client.is_active" :url="'/clients/deactivate-reactivate'" :id="client.id" class="pt-1"/> 
                            </div>
                            
                            <div class="w-10/12">    
                                <span class="text-lg font-semibold">{{ client.is_active ? 'Disconnect'  : 'Reconnect' }}</span>
                            </div>
                        </div>


                        <div class="w-full mt-4 inline-flex justify-center">
                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #0288D1"
                                @click="acticateConnection()" class="mr-1"
                                v-if="activeTab == 'clients' && client.status == 'Pending'"
                            >
                                Activate
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #0288D1"
                                @click="viewClient(client.reference)" class="mr-1"
                                v-if="activeTab == 'billing'"
                            >
                                VIEW
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #0288D1"
                                @click="viewPayment(client)" v-if="activeTab == 'cashiering' && !isViewPayment"
                                class="mr-1"
                            >
                                VIEW PAYMENTS
                            </button>

                            <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #EF5350"
                                @click="notify(client)" v-if="client.amount_to_pay > 0 && activeTab == 'cashiering'"
                            >
                                NOTIFY
                            </button>
                        </div>

                        <div class="w-full mt-4 flex flex-col" v-if="client.amount_to_pay > 0 && !!isViewPayment && activeTab == 'cashiering'">

                            <div class="w-full my-1">
                                <select style="width: 100%; border: 1px solid black; border-radius: 5px; height: 33px; text-align: center" v-model="month">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>

                            <div class="w-full my-1">
                                <input type="text" style="width: 100%; border: 1px solid black; border-radius: 5px; height: 33px; text-align: center" 
                                    @keypress="validate($event)" v-model="paymentAmount"
                                >
                            </div>

                            <div class="w-full my-1">
                                <button style="background: #000000; color: white; padding: 5px 10px 5px 10px; border-radius: 5px; background: #0288D1; width: 100%"
                                    @click="markAsPaid(client.id)" v-if="client.amount_to_pay > 0 && activeTab == 'cashiering'"
                                >
                                 SAVE PAYMENT
                                </button>

                                <span class="text-xs text-red-500" v-if="message">
                                    {{message}} 
                                </span>
                            </div>
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
                            <label for="address">House No/Street:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.house_no" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('house_no', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Barangay:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.street" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('street', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Municipality:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.town" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('town', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Province:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.province" style="text-transform: capitalize;">
                            <span class="text-xs text-red-500">{{validationError('province', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Contact #:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.phone">
                            <span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Serial #:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.serial">
                            <span class="text-xs text-red-500">{{validationError('serial', saveError)}} </span>
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
                pdf-orientation="portrait"
                pdf-content-width="100%"
                @hasDownloaded="hasDownloaded($event)"
                ref="or"
            >
                <section slot="pdf-content">
                    <div class="w-full flex justify-center items-center mt-5">
                        <img src="/images/logo.png" style="width: 80px; height: 80px"/>
                    </div>

                    <div class="w-full text-xs text-center font-bold">
                        Hydrolite Waterworks and Consumers Association
                    </div>

                    <div class="w-full text-xs text-center font-bold pb-6">
                        Brgy. Lumbang Calzada Calaca, Batangas
                    </div>

                    <div class="flex flex-col p-2 w-full h-screen mt-4">
                        <div class="w-full text-center text-md mt-2 font-bold">
                           OFFICIAL WATER BILLING RECEIPT
                        </div>

                        <div class="w-full flex flex-col mt-6">
                            <div class="mt-2 text-lg w-full mb-1 pb-1">
                                <span class="float-left">
                                    <b>Date:</b>
                                </span>

                                <span class="float-right mr-2" id="now">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1">
                                <span class="float-left">
                                    <b>Account #:</b>
                                </span>

                                <span class="float-right mr-2" id="reference">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Name:</b>
                                </span>

                                <span class="float-right mr-2" id="name">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Address:</b>
                                </span>

                                <span class="float-right mr-1 text-md" id="address">

                                </span>
                            </div>


                            <div class="mt-2 text-lg w-full mb-1 pb-1">
                                <span class="float-left">
                                    <b>Serial #:</b>
                                </span>

                                <span class="float-right mr-2" id="serial">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1">
                                <span class="float-left">
                                    <b>Cashier:</b>
                                </span>

                                <span class="float-right mr-2" id="cashier">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Month:</b>
                                </span>

                                <span class="float-right mr-2" id="month">

                                </span>
                            </div>
                            
                            <!-- <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Present Reading:</b>
                                </span>

                                <span class="float-right mr-2" id="present">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full pb-5" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Previous Reading:</b>
                                </span>

                                <span class="float-right mr-2" id="previous">

                                </span>
                            </div> -->

                            <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Other Charge(s):</b>
                                </span>

                                <span class="float-right mr-2" id="charges">

                                </span>
                            </div>

                            <!-- <div class="mt-2 text-lg w-full mt-5" id="testdiv">
                            </div> -->

                            <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Total Charges</b>
                                </span>

                                <span class="float-right mr-2" id="chargesAmount">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Penalty:</b>
                                </span>

                                <span class="float-right mr-2" id="penalty">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mt-5">
                                <span class="float-left">
                                    <b>Amount Paid:</b>
                                </span>

                                <span class="float-right mr-2" id="amount_paid">

                                </span>
                            </div>
<!-- 
                            <div class="mt-2 text-lg w-full mt-5 pb-5" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Total Balance:</b>
                                </span>

                                <span class="float-right mr-2" id="amount_to_pay">

                                </span>
                            </div> -->

                            <!-- <div class="mt-2 text-lg w-full mt-5 pb-5" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Total Bill:</b>
                                </span>

                                <span class="float-right mr-2" id="total">

                                </span>
                            </div> -->
                            

                            <div class="mt-2 text-md w-full mt-5">
                                You may check your bill online @https://water-billing-v2.onrender.com
                            </div>

                            <div class="mt-2 text-md w-full">
                                For any inquiries, please contact 09566814383/09657657443
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
import Dropdown from 'vue-simple-search-dropdown';

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table,
        Toggle,
        VueHtml2pdf,
        Dropdown
    },
    data(){
        return {
            columns: [
                'Name', 'Address', 'Account #'
            ],
            keys : [
                {
                    label: 'fullname',
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
                house_no: null,
                street: null,
                town: null,
                province: null,
                phone: null,
                serial: null
            },
            saveError: null,
            client: null,
            activeTab: 'dashboard',
            search: null,
            isViewPayment: false,
            payments: [],
            payment: null,
            paymentColumns: [
                'Month', 'Amount', 'Penalty', 'Charges', 'Total Bill', 'Balance', 'Due Date', 'Status'
            ],
            paymentKeys : [
                {
                    label: 'month',
                },
                {
                    label: 'amount',
                },
                {
                    label: 'penalty',
                },
                {
                    label: 'charges',
                },
                {
                    label: 'total',
                },
                {
                    label: 'amount_to_pay',
                },
                {
                    label: 'due_date',
                },
                {
                    label: 'status',
                }
            ],
            otherPaymentColumns: [
                'Month', 'Amount', 'Penalty', 'Charges', 'Total Bill',  'Amount Paid', 'Payment Date'
            ],
            otherPaymentKeys : [
                {
                    label: 'month',
                },
                {
                    label: 'amount',
                },
                {
                    label: 'penalty',
                },
                {
                    label: 'paid_charges', 
                },
                {
                    label: 'total',
                },
                {
                    label: 'history_payment', 
                },
                {
                    label: 'payment_date',
                },
            ],
            month: 1,
            paymentAmount: 0,
            message: null,
            selectedClient: null,
            clientData: null,
            hasSelected: false,
            unpaid_total: 0,
            months: [
                {
                    label: 'January',
                    value: 1
                },
                {
                    label: 'February',
                    value: 2
                },
                {
                    label: 'March',
                    value: 3
                },
                {
                    label: 'April',
                    value: 4
                },
                {
                    label: 'May',
                    value: 5
                },
                {
                    label: 'June',
                    value: 6
                },
                {
                    label: 'July',
                    value: 7
                },
                {
                    label: 'August',
                    value: 8
                },
                {
                    label: 'September',
                    value: 9
                },
                {
                    label: 'October',
                    value: 10
                },
                {
                    label: 'November',
                    value: 11
                },
                {
                    label: 'December',
                    value: 12
                },
            ],
            availableMonths: [],
            histories: []
        }
    },

    mounted() {
        this.clients = this.options.clients

        if(this.auth.user_type == 'cashier') {
            this.activeTab = 'cashiering'
        }
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
                    'Name', 'Address', 'Contact #', 'Account #', 'Serial #', 'Status'
                ]

                this.keys = [
                    {
                        label: 'fullname',
                    },
                    {
                        label: 'address',
                    },
                     {
                        label: 'phone',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'serial',
                    },
                    {
                        label: 'status',
                    },
                ]
            }

            if(arg == 'billing') {
                this.columns = [
                    'Name', 'Account #', 'Serial #', 'Amount to Pay', 'Charges', 'Due Date'
                ]

                this.keys = [
                    {
                        label: 'fullname',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'serial',
                    },
                    {
                        label: 'amount_to_pay',
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
                    'Name', 'Account #', 'Serial #', 'Amount to Pay', 'Charges', 'Total'
                ]

                this.keys = [
                    {
                        label: 'fullname',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'serial',
                    },
                    {
                        label: 'amount_to_pay',
                    },
                    {
                        label: 'other_fee',
                    },
                    {
                        label: 'total',
                    },
                ]
            }
        },
        clientData(arg){
            // console.log(arg)
            var self = this 

            document.getElementById("reference").innerHTML = arg.reference;
            document.getElementById("name").innerHTML = arg.name;
            document.getElementById("address").innerHTML = arg.address;
            document.getElementById("serial").innerHTML = arg.serial;
            document.getElementById("present").innerHTML = arg.present + ' mᶟ';
            document.getElementById("previous").innerHTML = arg.previous + ' mᶟ';
            document.getElementById("month").innerHTML = arg.month;
            document.getElementById("charges").innerHTML = arg.charges;
            document.getElementById("cashier").innerHTML = arg.cashier;
            document.getElementById("now").innerHTML = arg.now;
            document.getElementById("chargesAmount").innerHTML = '₱ ' + (parseFloat(arg.chargesAmount).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            // document.getElementById("amount_to_pay").innerHTML = '₱ ' + (parseFloat(arg.amount_to_pay).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("amount_paid").innerHTML = '₱ ' + (parseFloat(arg.amount_paid).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("penalty").innerHTML = '₱ ' + (parseFloat(arg.penalty).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("total").innerHTML = '₱ ' + (parseFloat(arg.total).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');


            setTimeout(() => {
                self.$refs.or.generatePdf()
            }, 3000)
        }
    },

    methods: {
        selectClient(arg){
            this.hasSelected = false

            this.selectedClient = arg

            if(arg.id) {
                this.viewPayment(arg)
                this.hasSelected = true
            }
            
        },
        createClient(){
            swal({
                title: "Are you sure to this new water bill connection ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
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

                                swal({
                                    title: "Water Bill",
                                    text: "You successfully created new connection.",
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

        acticateConnection(){
            swal({
                title: "Are you sure you want to activate this connection ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/clients/client/activate", { id: this.client.id })
                        .then(response => {
                            swal("Clients", "You successfully activated this connection.", "success");

                            location.reload()
                        })
                } 
            });
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
            swal({
                title: "Are you sure with this payment?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    var req = {
                        month: this.month,
                        client_id: client_id,
                        paymentAmount: this.paymentAmount
                    }

                    axios.post(this.$root.route + "/clients/client/mark-as-paid", req)
                        .then(response => {
                            // alert()
                            if(!response.data.message) {
                                swal("Client's Payments", "Payment save successfully.", "success");

                                var data = response.data.data

                                this.clientData = data

                                // this.displayData = response.data.display


                                // this.$refs.or.generatePdf()
                            } else {
                                this.message = response.data.message
                            }
                            
                        })
                }
            });

            
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
        }, 

        viewPayment(arg) {
            axios.post(this.$root.route + "/clients/client/view-payment", {client_id : arg.id})
				.then(response => {
                    this.payments = response.data.payments
                    this.unpaid_total = response.data.total
                    this.availableMonths = response.data.months
                    this.histories = response.data.histories
				})
        },

        validate(evt) {
            var theEvent = evt || window.event;

            // Handle paste
            if (theEvent.type === 'paste') {
                key = event.clipboardData.getData('text/plain');
            } else {
            // Handle key press
                var key = theEvent.keyCode || theEvent.which;
                key = String.fromCharCode(key);
            }
            var regex = /[0-9]|\./;
            if( !regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        },
        
    }
}

</script>

<style scoped>
.--input {
    width: 100%;
    height: 30px;
    border: 1px solid black;
    border-radius: 3px;
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