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
                        placeholder="Account #" v-model="form.reference"
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
                :paginate-elements-by-height="1000"
                :filename="Math.random().toString(36).slice(2)"
                :pdf-quality="2"
                :manual-pagination="false"
                pdf-format="a6"
                pdf-orientation="portrait"
                pdf-content-width="100vw"
                ref="receipt"
            >
                <section slot="pdf-content">
                    <div class="flex flex-col p-4 w-full h-screen">
                        <div class="w-full text-center text-xl mt-2 font-bold">
                            Water Billing System
                        </div>

                        <div class="w-full mt-4">
                            <p class="mt-2 text-lg">
                                <b>Name:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                {{ clientData.name }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Address:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                {{ clientData.address }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Account #:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                {{ clientData.reference }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Due Date:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                {{ clientData.date }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Bill:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                ₱ {{ clientData.amount.toFixed(2) }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Penalty:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                ₱ {{ clientData.penalty.toFixed(2) }}
                            </p>

                            <p class="mt-2 text-lg">
                                <b>Total Bill:</b>
                            </p>

                            <p class="mt-1 text-lg">
                                ₱ {{ clientData.total.toFixed(2) }}
                            </p>
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
            clientData: {
                name: null,
                address: null,
                reference: null,
                penalty: 0,
                amount: 0,
                date: null,
                total: 0
            },
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

                        this.clientData = {
                            name: data.client.name,
                            address: data.client.address,
                            reference: data.client.reference,
                            penalty: data.client.penalty,
                            amount: data.amount,
                            date: data.due_date,
                            total: data.total
                        }

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
            this.$refs.receipt.generatePdf()
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