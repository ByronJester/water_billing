<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2">
            <div class="w-full text-2xl pt-5 px-4 cursor-pointer">
                <span @click="back()" v-if="auth.role != 2">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </span>

                <span class="float-right">
                    {{ options.client.name }}
                </span>
            </div>

            <div class="w-full flex flex-col mt-2 md:mt-10">
                <!-- <div class="w-full mt-10">
                    <graph-line3d
                            style="w-full"
                            :height="400"
                            :axis-min="0"
                            :axis-max="Math.max( ...options.reports.amount )"
                            :padding-top="100"
                            :padding-bottom="100"
                            :depth="180"
                            :labels="options.reports.month"
                            :names="['Water Billing Report']"
                            :colors="[ '#246AC6']"
                            :values="options.reports.amount">
                        <note :text="'Year ' + new Date().getFullYear()" :align="'center'"></note>
                        <rotate3d></rotate3d>
                    </graph-line3d>
                </div> -->

                <div class="w-full mt-10">
                    <Table :columns="columns" :rows="options.payments" :keys="keys" :selected.sync="payment" class="w-full"/>
                </div>

                <div class="w-full">
                    <span class="float-right font-bold text-xl">
                        Total: ₱ {{ parseFloat(options.unpaid).toFixed(2) }}
                    </span>
                </div>

                <div id="receiptModal" class="receiptModal">
                    <!-- Modal content -->
                    <div class="receipt-content flex flex-col" :style="{'width': screenWidth <= 700 ? '90%' : '50%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                            Payment Receipt
                            </span>

                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <iframe :src="'/images/uploads/' + receipt" style="width: 100%; height: 600px"></iframe>
                        </div>
                    </div>
                </div>



                
            </div>
            
        </div>
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import { Inertia } from '@inertiajs/inertia';
import Table from "../Components/Table";

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table
    },
    data() {
        return {
            columns: [
                'Month', 'Amount', 'Penalty', 'Charges', 'Total Bill',  'Amount Paid' , 'Balance', 'Due Date', 'Status'
            ],
            keys : [
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
                    label: 'payment',
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
            payment: null,
            receipt: null,
            screenWidth: null
        }
    },
    created() {
        // this.client = this.options.client
        this.screenWidth = window.screen.width;
    },

    watch: {
        payment(arg){
            this.receipt = arg.receipt

            this.openModal()
        }
    },

    methods: {
        back(){
            Inertia.get(
                this.$root.route + '/clients' ,
                {
                    onSuccess: () => { },
                },
            );
        },
        openModal(arg){
            var modal = document.getElementById("receiptModal");
            modal.style.display = "block";
        },
        closeModal(){
            var modal = document.getElementById("receiptModal");
            modal.style.display = "none";

            this.payment = null
            this.receipt = null
        },
    }
}

</script>

<style scoped>
.receiptModal {
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
.receipt-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px; 
  border: 1px solid #888;
  width: 80%;
}
</style>