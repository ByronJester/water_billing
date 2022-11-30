<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-phone mr-3"> </i>INCIDENT REPORTING
                </span>
            </div>

            <div class="w-full h-full mt-5 flex flex-col md:flex-row ">

                <div class="mx-2 flex flex-col" :style="{width: screenWidth <= 700 ? '100%': '20%'}" v-if="auth.role == '2'">
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

                <div class="px-2" :style="{width: screenWidth <= 700 || auth.role == 1 ? '100%': '80%', 'margin-top': screenWidth <= 700 ? '10px': '0'}">
                    <Table :columns="columns" :rows="utilities" :keys="keys" :selected.sync="utility"/>
                </div>
            </div>

            <div id="statusModal" class="statusModal">
                <!-- Modal content -->
                <div class="status-content flex flex-col" :style="{'width': screenWidth <= 700 ? '100%' : '20%'}">
                    <div class="w-full">
                        <span class="text-lg font-bold">
                           Change Status
                        </span>

                        <span class="float-right cursor-pointer"
                            @click="closeStatusModal()"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                    </div>

                    <div class="w-full mt-5">
                        <select v-model="ir.status" class="w-full --input"
                            style="height: 40px; padding-left: 10px; border: 1px solid black"
                        >
                            <option value="pending">Pending</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="w-full mt-5">
                        <input type="number" class="--input" placeholder="Maintenance Charge" v-if="ir.status == 'completed'" v-model="ir.amount">
                    </div>

                    <div class="w-full mt-5">
                        <button class="w-full px-4 py-2 text-center" style="background: black; color:white;"
                            @click="changeStatus()"
                        >
                            Submit
                        </button>
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
            },
            ir: {
                id: null,
                status: null,
                amount: 0
            }
        }
    },

    created(){
        this.screenWidth = window.screen.width;

        this.form.user_id = this.auth.id

        this.utilities = this.options.utilities
    },

    watch: {
        utility(arg){
            if(arg.status != 'paid' && arg.status != 'completed') {
                if(this.auth.role == 1 && arg) {
                    this.openStatusModal()
                    this.ir.id = arg.id
                    this.ir.status = arg.status
                    this.ir.amount = arg.amount
                }
            }
            
        },
        'ir.status'(arg){
            
        }
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
        },

        openStatusModal(){
            var modal = document.getElementById("statusModal");
            modal.style.display = "block";
        },
        closeStatusModal(){
            this.utility = null
            this.ir.status = null
            var modal = document.getElementById("statusModal");
            modal.style.display = "none";
        },
        changeStatus(){
            axios.post(this.$root.route + "/clients/incident-report/change-status", this.ir)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
                        this.utilities = response.data.utilities
					}

                    this.closeStatusModal()
				})
        }
    }
}
</script>

<style scoped>
.--input {
    width: 100%;
    height: 40px;
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

.statusModal {
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
.status-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}
/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}
.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>