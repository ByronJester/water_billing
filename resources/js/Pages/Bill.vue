<template>
    <div class="w-full h-screen">
        <div class="flex flex-col p-4 w-full h-screen justify-center items-center"
            v-if="!isPrint"
        >
            <div class="w-full text-center mb-5">
                <span class="w-full text-2xl font-bold">
                    Generate Water Bill
                </span>
            </div>

            <div class="w-full">
                <input type="text" class="w-full  my-2 --login__register--input text-center"
                    placeholder="Line #" v-model="form.reference"
                >
                <span class="text-xs text-red-500">{{validationError('reference', saveError)}} </span>
            </div>

            <div class="w-full">
                <input type="number" class="w-full  my-2 --login__register--input text-center"
                    placeholder="Amount" v-model="form.amount"
                >
                <span class="text-xs text-red-500">{{validationError('amount', saveError)}} </span>
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

            <div class="w-full">
                <button class="w-full  my-2 --logout--button text-center"
                    @click="logout()"
                >
                    Logout
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
                <div class="flex flex-col p-4 w-full h-screen" v-if="clientData">
                    <div class="w-full text-center text-2xl mt-2 font-bold">
                        Water Billing System
                    </div>

                    <div class="w-full mt-8">
                        <p class="mt-2 text-xl">
                            <b>Name:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            {{ clientData.name }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Address:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            {{ clientData.address }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Line #:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            {{ clientData.reference }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Due Date:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            {{ clientData.date }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Bill:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            ₱ {{ clientData.amount }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Penalty:</b>
                        </p>

                         <p class="mt-1 text-lg">
                            ₱ {{ clientData.penalty }}
                        </p>

                        <p class="mt-4 text-xl">
                            <b>Total Bill:</b>
                        </p>

                        <p class="mt-1 text-lg">
                            ₱ {{ clientData.amount + clientData.penalty }}
                        </p>
                    </div>
                </div>
            </section>
        </VueHtml2pdf>


        
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import VueHtml2pdf from 'vue-html2pdf'

export default {
    components: {
        VueHtml2pdf
    },
    data(){
        return {
            form: {
                reference: null,
                amount: 0,
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

                        this.clientData = {
                            name: data.client.name,
                            address: data.client.address,
                            reference: data.client.reference,
                            penalty: data.client.penalty,
                            amount: data.amount,
                            date: data.due_date
                        }

                        this.form = {
							reference: null,
							amount: 0,
                            date: null
						}

						alert("Successfully generate bill.");

						this.saveError = null

                        this.$refs.receipt.generatePdf()

                        // location.reload()
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