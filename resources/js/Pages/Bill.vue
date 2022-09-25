<template>
    <div class="w-full h-screen">
        <div class="flex flex-col p-4 w-full h-screen justify-center items-center"
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
                <button class="w-full  my-2 --login__register--button text-center"
                    @click="generateBill()"
                >
                    Submit
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
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";

export default {
    data(){
        return {
            form: {
                reference: null,
                amount: 0
            },
            saveError: null
        }
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
						this.form = {
							reference: null,
							amount: 0
						}

						alert("Successfully generate bill.");

						this.saveError = null

                        location.reload()
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
	background: #303F9F;
	color: white
}
</style>