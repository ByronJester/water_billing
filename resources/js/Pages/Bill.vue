<template>
    <Navigation :auth="auth">
        <div class="w-full h-screen">
            <div class="flex flex-col p-4 w-full h-full mt-10"
                v-if="!isPrint"
            >
                <div class="w-full text-center mb-5">
                    <span class="w-full text-2xl font-bold">
                        Generate Water Bill
                    </span>
                </div>

                <div class="w-full">
                    <label class="font-bold text-sm">Serial #:</label>
                    <select class="--login__register--input w-full text-center text-sm" v-model="selected">
                        <option :value="client" v-for="client in options.clients" :key="client.id">
                            {{ client.serial_display }}
                        </option>
                    </select>
                </div>

                <div class="w-full mt-2">
                    <label class="font-bold text-sm">Account #:</label>
                    <input type="text" class="w-full  my-2 --login__register--input text-center text-sm"
                        v-model="form.reference" style="text-transform: uppercase"
                        :disabled="true"
                    >
                    <span class="text-xs text-red-500">{{validationError('reference', saveError)}} </span>
                </div>

                <div class="w-full mt-2" v-if="selected">
                    <label class="font-bold text-sm">Previous Reading:</label>
                    <input type="text" class="w-full  my-2 --login__register--input text-center text-sm"
                        v-model="selected.latest_consumed" style="text-transform: uppercase"
                        :disabled="true"
                    >
                    <span class="text-xs text-red-500">{{validationError('reference', saveError)}} </span>
                </div>

                <div class="w-full" mt-2>
                    <label class="font-bold text-sm">Current Reading:</label>
                    <input type="number" class="w-full  my-2 --login__register--input text-center text-sm"
                        placeholder="Current Reading" v-model="form.consumed_cubic_meter"
                    >
                    <span class="text-xs text-red-500">{{validationError('consumed_cubic_meter', saveError)}} </span>
                </div>

                <div class="w-full">
                    <label class="font-bold text-sm">Date:</label>
                    <input type="date" class="w-full  my-2 --login__register--input text-center text-sm"
                        v-model="form.date"
                        :disabled="true"
                    >
                    <span class="text-xs text-red-500">{{validationError('date', saveError)}} </span>
                </div>

                <div class="w-full mt-2">
                    <button class="w-full  my-2 --login__register--button text-center"
                        @click="generateBill()"
                        :disabled="!form.reference"
                        :class="{'cursor-not-allowed': !form.reference}"
                    >
                        Generate Bill
                    </button>

                    <span class="text-xs text-red-500" v-if="errMessage">{{errMessage}}</span>
                </div>

                <div class="w-full mt-2">
                    <button class="w-full  my-2 --void--button text-center"
                        :disabled="!form.reference"
                        @click="voidLastReading()"
                        :class="{'cursor-not-allowed': !form.reference}"
                    >
                        Void Last Reading
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
                @hasDownloaded="downloaded($event)"
                ref="receipt"
            >
                <section slot="pdf-content">
                    <div class="w-full flex justify-center items-center mt-2">
                        <img src="/images/logo.png" style="width: 60px; height: 60px"/>
                    </div>

                    <div class="w-full text-xs text-center font-bold">
                        Hydrolite Waterworks and Consumers Association
                    </div>

                    <div class="w-full text-xs text-center font-bold">
                        Brgy. Lumbang Calzada Calaca, Batangas
                    </div>

                    <div class="flex flex-col p-2 w-full h-screen mt-5">
                        <div class="w-full text-center text-md font-bold">
                           Billing Notice
                        </div>

                        <div class="w-full text-center text-md mt-2 font-bold">
                           FOR THE MONTH OF <span id="month" class="mx-1"></span> <span id="year"></span>
                        </div>

                        <div class="w-full flex flex-col mt-2">
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
                                    <b>Account #:</b>
                                </span>

                                <span class="float-right mr-2" id="reference">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1" style="border-bottom: dashed black;">
                                <span class="float-left">
                                    <b>Serial #:</b>
                                </span>

                                <span class="float-right mr-2" id="serial">

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

                            <div class="mt-2 text-lg w-full mt-10">
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
                                    <b>Charges:</b>
                                </span>

                                <span class="float-right mr-2" id="charges">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full">
                                <span class="float-left">
                                    <b>Unpaid Month:</b>
                                </span>

                                <span class="float-right mr-2" id="count">

                                </span>
                            </div>

                            <div class="mt-2 text-lg w-full mb-1 pb-1 mt-5" style="border-bottom: dashed black;">
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
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import VueHtml2pdf from 'vue-html2pdf'
import Navigation from '../Layouts/Navigation.vue'
import Dropdown from 'vue-simple-search-dropdown';

export default {
    props: ['auth', 'options'],
    components: {
        VueHtml2pdf,
        Navigation,
        Dropdown
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
            isPrint: false,
            selected: null,
            errMessage: null
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
            swal({
                title: "Are you sure to generate this bill ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/bills/generate", this.form)
                        .then(response => {
                            if(response.data.status == 422) {
                                if(!!response.data.errMessage) {
                                    this.errMessage = response.data.errMessage
                                } else {
                                    this.saveError = response.data.errors 
                                }
                            } else {
                                var data = response.data.data

                                this.clientData = data

                                swal("Generate Bill", "You successfully generated bill.", "success");

                                this.saveError = null

                                

                                // location.reload()
                            }  
                        })
                }
            });
            
        },

        voidLastReading() {
            swal({
                title: "Are you sure to void the latest bill ?",
                text: "You will not be able to recover this bill.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/bills/void", { client_id: this.selected.id })
                        .then(response => {
                            location.reload()
                        })
                }
            });

            
        },

        downloaded(evt) {
            // location.reload()
            
            var file = new File([evt], "receipt.pdf", { lastModified: new Date().getTime(), type: evt.type })

            console.log(file)

            var form_data = new FormData()

            form_data.append('reference', this.form.reference)
            form_data.append('image', file)

            axios.post(this.$root.route + "/bills/save-receipt", form_data)
                        .then(response => {
                            location.reload()
                            // console.log(response)
                        })
        }
    },
    watch: {
        selected(arg){
            this.form.reference = arg.reference
        },
        clientData(arg) {
            var self = this 

            // ₱

            document.getElementById("month").innerHTML = arg.month;
            document.getElementById("year").innerHTML = arg.year;
            document.getElementById("name").innerHTML = arg.client.fullname;
            document.getElementById("address").innerHTML = arg.client.address;
            document.getElementById("reference").innerHTML = arg.client.reference;
            document.getElementById("serial").innerHTML = arg.client.serial;
            document.getElementById("penalty").innerHTML = '₱ ' + (parseFloat(arg.penalty).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("pres").innerHTML = '₱ ' + (parseFloat(arg.pres).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("prev").innerHTML = '₱ ' + (parseFloat(arg.prev).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("due_date").innerHTML = arg.client.due_date;
            document.getElementById("total").innerHTML = '₱ ' + (parseFloat(arg.total).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById("reader").innerHTML = arg.reader;
            document.getElementById("date").innerHTML = arg.date; 
            document.getElementById("consumption").innerHTML = arg.consumption + ' mᶟ';
            document.getElementById("message").innerHTML = arg.message; 
            document.getElementById("count").innerHTML = arg.count;
            document.getElementById("charges").innerHTML = '₱ ' + (parseFloat(arg.charges).toFixed(2)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

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

.--void--button {
	height: 40px;
	border-radius: 30px;
	background: #FF2B2B;
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
