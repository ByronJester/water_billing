<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-users mr-3"> </i>CUBIC METER
                </span>
            </div>

            <div class="w-full h-full mt-5 flex flex-row">
                <div style="width: 80%" class="mx-2">
                    <Table :columns="columns" :rows="bills" :keys="keys" :selected.sync="bill"/>
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
    data(){
        return {
            newBill: false,
            columns: [
                'Amount', 'Date', 'Personnel'
            ],
            keys : [
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
            bills: [],
            bill: null,
            form: {
                amount: 0
            },
            saveError: null
        }
    },

    created(){
        this.bills = this.options.bills
        console.log(this.bills)
    },

    methods: {
        createBill(){
			axios.post(this.$root.route + "/settings/create-bill", this.form)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						this.form = {
							amount: null,
							date: null
						}

						alert("New bill successfully created.");

						this.saveError = null

						location.reload()
					}
				})
        },
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