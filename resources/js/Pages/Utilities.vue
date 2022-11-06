<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-phone mr-3"> </i>INCIDENT REPORTING
                </span>
            </div>

            <div class="w-full h-full mt-5 flex flex-col md:flex-row ">

                <div class="mx-2 flex flex-col" :style="{width: screenWidth <= 700 ? '100%': '20%'}">
                    <div class="pb-3"
                        style="width:100%;"
                    >
                        <span class="text-xl">
                            <i class="fa-solid fa-plus mr-2"></i>Generate Incident Report
                        </span>
                        
                    </div>

                    <div class="py-5 px-3"
                        style="width:100%; border: 1px solid black; border-radius: 5px"
                    >
                        <div>
                            <label for="name">Description:</label><br>
                            <textarea :rows="screenWidth <= 700 ? '3' : '10'" cols="50" class="w-full p-2 mt-2"
                                style="border: 1px solid black;" v-model="form.description"
                            ></textarea>
                            <span class="text-xs text-red-500">{{validationError('description', saveError)}} </span>
                        </div>

                        <div class="mt-6">
                            <button class="--btn py-2" @click="generateIncidentReport()">
                                Generate
                            </button>
                        </div>
                        
                    </div>
                </div>

                <div class="mx-2" :style="{width: screenWidth <= 700 ? '100%': '80%', 'margin-top': screenWidth <= 700 ? '10px': '0'}">
                    <Table :columns="columns" :rows="utilities" :keys="keys" :selected.sync="utility"/>
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
                'Name', 'Address', 'Description', 'Status'
            ],
            keys : [
                {
                    label: 'client_name',
                },
                {
                    label: 'client_address'
                },
                {
                    label: 'description',
                },
                {
                    label: 'status',
                },
            ],
            utilities: [],
            utility: null,
            saveError: null,
            screenWidth: null,
            form: {
                user_id: null,
                description: null
            }
        }
    },

    created(){
        this.screenWidth = window.screen.width;

        this.form.user_id = this.auth.id

        this.utilities = this.options.utilities
    },

    methods: {
        generateIncidentReport(){
            axios.post(this.$root.route + "/clients/incident-report", this.form)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						alert("Incident report created.");

						this.saveError = null

                        location.reload()
					}
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