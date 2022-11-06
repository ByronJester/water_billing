<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full mt-3">
                <span class="text-2xl ml-2 font-bold">
                    <i class="fa-solid fa-user mr-3"> </i>Profile
                </span>
            </div>

            <div class="w-full h-full mt-5 flex justify-center items-center">
                <div class="flex flex-col w-full md:w-1/4" style="border: 1px solid black; border-radius: 10px">
                    <div class="px-5 py-3">
                        <label for="name">Name:</label><br>
                        <input type="text" class="--input py-1" v-model="form.name">
                        <span class="text-xs text-red-500">{{validationError('name', saveError)}} </span>
                    </div>

                    <div class="px-5 py-3">
                        <label for="name">Contact:</label><br>
                        <input type="text" class="--input py-1" v-model="form.phone">
                        <span class="text-xs text-red-500">{{validationError('phone', saveError)}} </span>
                    </div>

                    <div class="px-5 py-3">
                        <label for="name">Email:</label><br>
                        <input type="text" class="--input py-1" v-model="form.email">
                        <span class="text-xs text-red-500">{{validationError('email', saveError)}} </span>
                    </div >

                    <div class="px-5 py-3">
                        <label for="name">Password:</label><br>
                        <input type="password" class="--input py-1" v-model="form.password" placeholder="********">
                        <span class="text-xs text-red-500">{{validationError('password', saveError)}} </span>
                    </div>

                    <div class="px-5 py-3">
                        <label for="name">Confirm Password:</label><br>
                        <input type="password" class="--input py-1" v-model="form.confirm_password" placeholder="********">
                        <span class="text-xs text-red-500">{{validationError('confirm_password', saveError)}} </span>
                    </div>

                    <div class="px-5 pt-3 pb-5">
                        <button class="--btn py-2" @click="submit()">
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
            form: {
                id: null,
                name: null,
                phone: null,
                email: null,
                password: null,
                confirm_password: null
            },
            saveError: null
        }
    },

    created(){
        this.form.id = this.auth.id
        this.form.name = this.auth.name
        this.form.phone = this.auth.phone
        this.form.email = this.auth.email
    },

    methods: {
        submit(){
            var req = this.form

            if(!req.password && !req.confirm_password) {
                delete req["password"];
                delete req["confirm_password"];
            }

            axios.post(this.$root.route + "/users/edit-profile", req)
				.then(response => {
					if(response.data.status == 422) {
						this.saveError = response.data.errors 
					} else {
						alert("Profile successfully updated.");

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
</style>