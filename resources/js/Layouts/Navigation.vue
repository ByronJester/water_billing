<template>
    <div class="w-full" style="min-height: 100vh; height: 100%">
        <div class="w-full flex flex-row --header" style="height: 10%">
            <div :style="{'width': screenWidth <= 700 ? '20%' : '100%'}">
                <img src="/images/logo.png" class="p-2 --logo"/>
            </div>

            <div class="flex justify-end items-center" style="color: #ffffff" :style="{'width': screenWidth <= 700 ? '80%' : '100%' }">
                <p class="mx-1 --text cursor-pointer" @click="changeActive('/clients')" v-if="auth.user_type == 'admin' || auth.user_type == 'staff'">
                    <span class="mx-2"
                        :style="{'border-bottom': active === '/clients' ? '1px solid #ffffff' : 'none'}"
                    > 
                        DASHBOARD
                    </span>
                </p>
                

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/settings')" v-if="auth.user_type == 'admin' || auth.user_type == 'staff'">
                    <span  class="mx-2"
                        :style="{'border-bottom': active === '/settings' ? '1px solid #ffffff' : 'none'}"
                    > 
                        CUBIC METER
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/users/profile')">
                    <span class="mx-2"
                        :style="{'border-bottom': active === '/users/profile' ? '1px solid #ffffff' : 'none'}"
                    > 
                        PROFILE
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/clients/' + auth.reference)" v-if="auth.user_type == 'client'">
                    <span class="mx-2"
                        :style="{'border-bottom': active === '/clients/' + auth.reference ? '1px solid #ffffff' : 'none'}"
                    > 
                        BILLING
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/clients/view/utilities')" v-if="auth.user_type == 'client' || auth.user_type == 'utility'">
                    <span  class="mx-2"
                        :style="{'border-bottom': active === '/clients/view/utilities'? '1px solid #ffffff' : 'none'}"
                    > 
                        INCIDENT REPORTING
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/bills')" v-if="auth.user_type == 'reader'">
                    <span class="mx-2"
                        :style="{'border-bottom': active === '/bills' ? '1px solid #ffffff' : 'none'}"
                    > 
                        METER READING
                    </span>
                </p>

                

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/users')" v-if="auth.user_type == 'admin'">
                    <span class="mr-2"
                        :style="{'border-bottom': active === '/users' ? '1px solid #ffffff' : 'none'}"
                        
                    > 
                        MAINTENANCE
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/reports')" v-if="auth.user_type == 'admin' || auth.user_type == 'staff'">
                    <span class="mr-2"
                        :style="{'border-bottom': active === '/reports' ? '1px solid #ffffff' : 'none'}"
                        
                    > 
                        REPORTS
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer" @click="changeActive('/utilities')" v-if="auth.user_type == 'admin'">
                    <span class="mr-2"
                        :style="{'border-bottom': active === '/utilities' ? '1px solid #ffffff' : 'none'}"
                        
                    > 
                        UTILITIES
                    </span>
                </p>
                

                <!-- <p class="mx-1 --text cursor-pointer">
                    <span class="mx-2"
                    > 
                        MAINTENANCE
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer">
                    <span class="mx-2"
                    > 
                        REPORT
                    </span>
                </p>

                <p class="mx-1 --text cursor-pointer">
                    <span class="mx-2"
                    > 
                        UTILITIES
                    </span>
                </p> -->

                <p class="mx-1 --text cursor-pointer" @click="logout()" style="border-left: 1px solid #ffffff">
                    <i class="fa-solid fa-door-open fa-lg mx-2"></i> 
                </p>
            </div>
        </div>

        <div class="w-full" style="height: 90%">
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
            tabs: [],
            screenWidth: null
        }
	},

    created(){
        this.screenWidth = window.screen.width
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

.--header {
    /* background: -webkit-radial-gradient(skyblue, navy); */
    background: navy;
}

.--text {
    font-size: calc(.1em + 1vw)
}

.--logo {
    width: 100px;
    height: 100px;
}

@media screen and (max-width: 600px) {
    .--text {
        font-size: 9px;
    }

    .--logo {
        width: 60px;
        height: 60px;
    }
}

</style>
