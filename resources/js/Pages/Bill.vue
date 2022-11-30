<template>
    <Navigation :auth="auth">
        <div class="w-full h-screen">
            <div class="flex flex-col p-4 w-full h-full mt-20"
                v-if="!isPrint"
            >
                <div class="w-full text-center mb-5">
                    <span class="w-full text-2xl font-bold">
                        Generate Water Bill
                    </span>
                </div>

                <div class="w-full">
                    <input type="text" class="w-full  my-2 --login__register--input text-center"
                        placeholder="Account #" v-model="form.reference" style="text-transform: uppercase"
                    >
                    <span class="text-xs text-red-500">{{validationError('reference', saveError)}} </span>
                </div>

                <div class="w-full">
                    <input type="number" class="w-full  my-2 --login__register--input text-center"
                        placeholder="Consumed Cubic Meter" v-model="form.consumed_cubic_meter"
                    >
                    <span class="text-xs text-red-500">{{validationError('consumed_cubic_meter', saveError)}} </span>
                </div>

                <div class="w-full">
                    <input type="date" class="w-full  my-2 --login__register--input text-center"
                        v-model="form.date"
                    >
                    <span class="text-xs text-red-500">{{validationError('date', saveError)}} </span>
                </div>

                <div class="w-full">
                    <button class="w-full  my-2 --login__register--button text-center"
                        @click="generateBill()"
                    >
                        Generate Bill
                    </button>
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
                pdf-format="a6"
                pdf-orientation="portrait"
                pdf-content-width="100%"
                ref="receipt"
            >
                <section slot="pdf-content">
                    <div class="flex flex-col p-2 w-full h-screen">
                        <div class="w-full text-center text-xl mt-2 font-bold">
                           Billing Notice
                        </div>

                        <div class="w-full text-center text-xl mt-2 font-bold inline-flex">
                           FOR THE MONTH OF <span id="month" class="mx-1"></span> <span id="year"></span>
                        </div>

                        <div class="w-full flex flex-col mt-3">
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

                            <div class="mt-2 text-lg w-full mb-1 pb-1" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Account #:</b>
                                </span>

                                <span class="float-right mr-2" id="reference">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Previous Reading:</b>
                                </span>

                                <span class="float-right mr-2" id="prev">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Present Reading:</b>
                                </span>

                                <span class="float-right mr-2" id="pres">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Consumption:</b>
                                </span>

                                <span class="float-right mr-2" id="consumption">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Penalty(10%):</b>
                                </span>

                                <span class="float-right mr-2" id="penalty">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Unpaid Month:</b>
                                </span>

                                <span class="float-right mr-2" id="count">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Due Date: </b>
                                </span>

                                <span class="float-right mr-2" id="due_date">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Total Bill: </b>
                                </span>

                                <span class="float-right mr-2" id="total">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Meter Reader Name: </b>
                                </span>

                                <span class="float-right mr-2" id="reader">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Date Read: </b>
                                </span>

                                <span class="float-right mr-2" id="date">

                                </span>
                            </div>

                            <div class="mt-2 text-md w-full font-bold" id="message">

                            </div>

                            <div class="mt-2 text-md w-full">
                                You may check your bill online @https://water-billing-6mb6.onrender.com
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
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import VueHtml2pdf from 'vue-html2pdf'
import Navigation from '../Layouts/Navigation.vue'

export default {
    props: ['auth', 'options'],
    components: {
        VueHtml2pdf,
        Navigation
    },
    data(){
        return {
            form: {
                reference: null,
                consumed_cubic_meter: 0,
                date: null
            },
            saveError: null,
            clientData: null,
            isPrint: false
        }
    },

    created(){
        var date = new Date();

        var currentDate = date.toISOString().slice(0,10);
        this.form.date = currentDate
    },

    methods: {
        logout(){ 
            Inertia.post(this.$root.route + "/users/logout", {}, 
			{
				onSuccess: (res) => {
				},
				orError: (err) => {
				}
			});
        },

        generateBill(){
            axios.post(this.$root.route + "/bills/generate", this.form)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
                        var data = response.data.data

                        this.clientData = data

                        this.form = {
							reference: null,
							consumed_cubic_meter: 0,
                            date: null
						}

						alert("Successfully generate bill.");

						this.saveError = null

                        

                        // location.reload()
					}  
				})
        }
    },
    watch: {
        clientData(arg) {
            var self = this 

            // ₱

            document.getElementById("month").innerHTML = arg.month;
            document.getElementById("year").innerHTML = arg.year;
            document.getElementById("name").innerHTML = arg.client.name;
            document.getElementById("address").innerHTML = arg.client.address;
            document.getElementById("reference").innerHTML = arg.client.reference;
            document.getElementById("penalty").innerHTML = '₱ ' + (parseFloat(arg.client.penalty).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("pres").innerHTML = '₱ ' + (parseFloat(arg.pres).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("prev").innerHTML = '₱ ' + (parseFloat(arg.prev).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("due_date").innerHTML = arg.client.due_date;
            document.getElementById("total").innerHTML = '₱ ' + (parseFloat(arg.total).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("reader").innerHTML = arg.reader;
            document.getElementById("date").innerHTML = arg.date; 
            document.getElementById("consumption").innerHTML = arg.consumption + ' mᶟ';
            document.getElementById("message").innerHTML = arg.message;
            document.getElementById("count").innerHTML = arg.count;

            setTimeout(() => {
                self.$refs.receipt.generatePdf()
            }, 3000)

            
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

.--logout--button {
	height: 40px;
	border-radius: 30px;
	background: #F96161;
	color: white
}

.--print--button {
	height: 40px;
	border-radius: 30px;
	background: #303F9F;
	color: white
}
</style>