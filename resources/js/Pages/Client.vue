<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2">
            <div class="w-full text-2xl pt-5 px-4 cursor-pointer">
                <span @click="back()" v-if="auth.role != 2">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </span>

                <span class="float-right">
                    {{ options.client.name }} - {{ options.client.reference }}
                </span>
            </div>

            <div class="w-full flex flex-col mt-2 md:mt-10">
                <!-- <div class="w-full mt-10">
                    <graph-line3d
                            style="w-full"
                            :height="400"
                            :axis-min="0"
                            :axis-max="Math.max( ...options.reports.amount )"
                            :padding-top="100"
                            :padding-bottom="100"
                            :depth="180"
                            :labels="options.reports.month"
                            :names="['Water Billing Report']"
                            :colors="[ '#246AC6']"
                            :values="options.reports.amount">
                        <note :text="'Year ' + new Date().getFullYear()" :align="'center'"></note>
                        <rotate3d></rotate3d>
                    </graph-line3d>
                </div> -->

                <div class="w-full mt-10">
                    <Table :columns="columns" :rows="options.payments" :keys="keys" :selected.sync="payment"/>
                </div>

                
            </div>
            
        </div>
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import { Inertia } from '@inertiajs/inertia';
import Table from "../Components/Table";

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table
    },
    data() {
        return {
            columns: [
                'Month', 'Amount', 'Due Date', 'Status'
            ],
            keys : [
                {
                    label: 'month',
                },
                {
                    label: 'amount',
                },
                {
                    label: 'due_date',
                },
                {
                    label: 'status',
                }
            ],
            payment: null
        }
    },
    created() {
        console.log(this.options)
        // this.client = this.options.client
    },

    methods: {
        back(){
            Inertia.get(
                this.$root.route + '/clients' ,
                {
                    onSuccess: () => { },
                },
            );
        }
    }
}

</script>