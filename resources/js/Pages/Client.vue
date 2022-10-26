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

            <div class="w-full flex flex-row mt-10">
                <div class="w-2/4 px-2 mt-20">
                    <Table :columns="columns" :rows="options.payments" :keys="keys" :selected.sync="payment"/>
                </div>

                <div class="w-3/4">
                    <graph-line
                            :width="900"
                            :height="600"
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
                        <tooltip :names="['Water Billing Report']" :position="'left'"></tooltip>
                        <legends :names="['Water Billing Report']"></legends>
                        <guideline :tooltip-y="true"></guideline>
                    </graph-line>
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