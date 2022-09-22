<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-users mr-3"> </i>Clients
                </span>
            </div>

            <div class="w-full h-full mt-5 flex flex-row">

                <div style="width: 80%" class="mx-2">
                    <Table :columns="columns" :rows="clients" :keys="keys"/>
                </div>

                <div style="width: 20%" class="mx-2 flex flex-col">
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
                            <label for="name">Name:</label><br>
                            <input type="text" id="name" name="name" class="--input py-4" v-model="form.name">
                            <span class="text-xs text-red-500">{{validationError('name', saveError)}} </span>
                        </div>

                        <div class="mt-4">
                            <label for="address">Address:</label><br>
                            <input type="text" id="address" name="address" class="--input py-4" v-model="form.address">
                            <span class="text-xs text-red-500">{{validationError('address', saveError)}} </span>
                        </div>

                        <div class="mt-6">
                            <button class="--btn py-2" @click="createClient()">
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
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table
    },
    data(){
        return {
            columns: [
                'Name', 'Address', 'Account #'
            ],
            keys : [
                {
                    label: 'name',
                },
                {
                    label: 'address',
                },
                {
                    label: 'reference',
                },
            ],
            clients: [],
            form: {
                name: null,
                address: null
            },
            saveError: null
        }
    },

    mounted() {
        this.clients = this.options.clients
    },

    methods: {
        createClient(){
            let headers = {
				'Access-Control-Allow-Origin': '*',
				'Access-Control-Allow-Methods': 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS',
				'Access-Control-Allow-Headers': 'Content-Type'
			}
			
			axios.post(this.$root.route + "/clients/client/create", this.form)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						this.form = {
							name: null,
							address: null
						}

						alert("New connection successfully created.");

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