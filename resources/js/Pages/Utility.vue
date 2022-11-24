<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
             <div class="w-full flex flex-row mb-3 font-bold" style="height: 50px; border-bottom: 1px solid black">
                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'archive'" :class="{'bg-blue-300': activeTab == 'archive' }">
                    ARCHIVES
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'trail'" :class="{'bg-blue-300': activeTab == 'trail' }">
                    AUDIT TRAILS
                </div>
            </div>

            <div class="w-full" v-if="activeTab == 'archive'">
                <div class="w-full mt-5" v-if="client">
                    <Toggle :value="client.is_active" :url="'/clients/deactivate-reactivate'" :id="client.id" class="mr-1 pt-1"/> <span class="font-bold"> RECONNECT ACCOUNT #: {{client.reference}} </span>
                </div>

                <table class="w-full mt-5">
                    <tr class="text-center">
                        <th v-for="column in ['FIRST NAME', 'LAST NAME', 'ADDRESS', 'ACCOUNT #']" :key="column">
                            {{ column }}
                        </th>
                    </tr>

                    <tr class="text-center"
                        v-for="(l, index) in options.clients" :key="index"
                    >
                        <td v-for="(k, i) in [{label: 'first_name'}, {label: 'last_name'}, {label: 'address'}, {label: 'reference'},]" :key="i" class="cursor-pointer"
                            :class="{'--active__color': !!client && client.id == l.id }"
                             @click="client = l"
                        >
                            <span>{{options.clients[index][k.label] }}</span> 
                        </td>
                    </tr>

                </table> 
            </div>

            <div class="w-full" v-if="activeTab == 'trail'">
                <table class="w-full">
                    <tr class="text-center">
                        <th v-for="column in ['LOGS']" :key="column">
                            {{ column }}
                        </th>
                    </tr>

                    <tr class="text-center"
                        v-for="(l, index) in options.trails" :key="index"
                    >
                        <td v-for="(k, i) in [{label: 'description'}]" :key="i" class="cursor-pointer">
                            <span>{{options.trails[index][k.label] }}</span> 
                        </td>
                    </tr>

                </table>
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
            activeTab: 'archive',
            client: null
        }
    },

    created(){
        
    },

    watch: {
        client(arg) {
        }
    },

    methods: {

    }
}
</script>

<style scoped>
table {
    border-collapse: collapse;
    border-radius: 5px;
    border-style: hidden;
    box-shadow: 0 0 0 1px black;
}

td {
    border: 1px solid black;
}

th {
    border: 1px solid black;
    background: navy;
    color: white;
}

.--active__color {
    background: #B0BEC5;
}
</style>