<template>
    <div class="w-screen h-screen flex flex-row">
        <div class="--left__panel" @mouseover="biggerWidth()" @mouseleave="smallerWidth()"
            :style="{ 'width': leftPanel}"
        >
            <ul class="pl-2 pt-10 --ul__caption text-bold w-full">
                <li class="mt-1 mb-10 cursor-pointer"
                    @click="changeActive('/users')"
                    v-if="auth.user_type == 'admin' || auth.user_type == 'staff'"
                >
                    <i class="fa-solid fa-user-group fa-lg mx-2"></i> 
                    <span v-if="isHover" class="mx-2"
                        :style="{'border-bottom': active === '/users' ? '1px solid black' : 'none'}"
                    > 
                        USERS
                    </span>
                </li>

                <li class="mt-1 mb-10 cursor-pointer"
                    @click="changeActive('/clients')"
                    v-if="auth.user_type == 'admin' || auth.user_type == 'staff'"
                >
                    <i class="fa-sharp fa-solid fa-users-between-lines fa-lg mx-2"></i>
                    <span v-if="isHover" class="mx-2"
                        :style="{'border-bottom': active === '/clients' ? '1px solid black' : 'none'}"
                    > 
                        CLIENTS
                    </span>
                </li>

                <li class="mt-1 mb-10 cursor-pointer"
                    @click="changeActive('/settings')"
                    v-if="auth.user_type == 'admin' || auth.user_type == 'staff'"
                >
                    <i class="fa-solid fa-gears fa-lg mx-2"></i>
                    <span v-if="isHover" class="mx-2"
                        :style="{'border-bottom': active === '/settings' ? '1px solid black' : 'none'}"
                    > 
                        SETTINGS
                    </span>
                </li>

                <li class="mt-1 mb-10 cursor-pointer"
                    @click="changeActive('/clients/' + auth.reference)"
                    v-if="auth.user_type == 'client'"
                >
                    <i class="fa-sharp fa-solid fa-hand-holding-droplet fa-lg mx-2"></i>
                    <span v-if="isHover" class="mx-2"
                        :style="{'border-bottom': active === '/clients/' + auth.reference ? '1px solid black' : 'none'}"
                    > 
                        CONNECTION
                    </span>
                </li>

                <li class="mt-1 mb-10 cursor-pointer"
                    @click="changeActive('/users/profile')"
                >
                    <i class="fa-solid fa-user-pen fa-lg mx-2"></i>
                    <span v-if="isHover" class="mx-2"
                        :style="{'border-bottom': active === '/users/profile' ? '1px solid black' : 'none'}"
                    > 
                        PROFILE
                    </span>
                </li>

                
            </ul>

            <ul class="--ul__caption absolute text-bold w-full"
                style="bottom: 0.2rem;"
            >
                <hr>
                <li class="my-5 px-2 cursor-pointer" @click="logout()">
                    <i class="fa-solid fa-door-open fa-lg mx-2"></i> 
                    <span v-if="isHover" class="mx-2"> LOGOUT </span>
                </li>
            </ul>
        </div>

        <div class="--right__panel"
            :style="{ 'width': rightPanel}"
        >
            <slot></slot>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
	props:['auth'],
	data(){
        return {
            leftPanel: '3%',
            rightPanel: '97%',
            isHover: false,
            active: window.location.pathname,
            tabs: []
        }
	},

    created(){

    },

	methods: {
        biggerWidth() {
            this.leftPanel = '9%';
            this.rightPanel = '91%';
            this.isHover = true
        },

        smallerWidth(){
            this.leftPanel = '3%';
            this.rightPanel = '97%';
            this.isHover = false
        },

        logout(){
            Inertia.post(this.$root.route + "/users/logout", {},
			{
				onSuccess: (res) => {
				},
				orError: (err) => {
				}
			});
        },

        hasAccess(arg){
            return this.tabs.includes(arg)
        },

        changeActive(arg){
            Inertia.get(
                this.$root.route + arg,
                {
                    onSuccess: () => { },
                },
            );
        }
	}
}

</script>

<style scoped>
.--left__panel {
    background: #0097A7;
    width: 15%;
}

.--right__panel {
    background: #ffffff;
}

.--ul__caption {
    color: black !important;
    font-weight: bold;
}

</style>
